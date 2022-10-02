<?php
namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * SearchForm is the model behind the search form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class SearchForm extends BaseModel
{
	var $searchString, $searchAddress, $page;
	
	public function rules(){
		return [
				[['searchString', 'searchAddress', 'page'], 'safe'],
		];
	}
	
	public function attributeLabels()
	{
		return [
				'searchString' => Yii::t('app', 'Enter name, especialty or procedure.'),
				'searchAddress' => Yii::t('app', 'Enter zip code or city.'),
		];
	}
	
	public function search($params){
		
		$this->load($params);
		
		$limit = 10;
		$page = $this->page ? $this->page : 1;
		$offset = ($page-1)*$limit;
		
		$query = new Query();
		$query->from('doctor');
		$query->innerJoin('office', 'office.doctor_id = doctor.id')
		->innerJoin('address', 'office.address_id = address.id');
		$location = $this->getGeoLocation();
		if (!$location && !Yii::$app->user->isGuest){
			//Get the coordinates from the current user
			$user = Yii::$app->user->identity;
			/* @var \common\models\User $user */
			
			if ($user->isPatient() && $address = $user->patient->address){
				/* @var \common\models\Address $address */
				if ($address->lat && $address->lng)
					$location = ['lat' => $address->lat, 'lng' => $address->lng]; 
			}
		}
		$geo_columns = [];
		$geo_sort = '';
		if ($location){
			$lat = $location['lat'];
			$lng = $location['lng'];
			
			
			$location_point = "ST_SetSRID( ST_MakePoint( $lat::float,$lng::float),4326 )::geography";
			$doctor_address_point = "ST_SetSRID( ST_MakePoint(address.lat::float,address.lng::float ),4326 )::geography";
			$distance = "ST_Distance( $doctor_address_point, $location_point)";
			$geo_columns = [			
					"CASE WHEN (address.lat IS NULL) OR (address.lng IS NULL) OR (address.lat = '') OR (address.lng = '') THEN NULL ELSE $distance END AS distance"
			];			
			
			$geo_sort = ', distance';
		}
		
		

		$average_subquery = $this->getAverageSubquery();
		$query->select(ArrayHelper::merge([
			'doctor.id AS doctor_id',
			'office.id AS office_id',
			"($average_subquery) AS rating"
		],$geo_columns))
		
		->orderBy('rating DESC'.$geo_sort);
		
		if ($this->searchString){
			$searchText = $this->searchString;

			$query->leftJoin('user', 'doctor.user_id = "user".id')
			->leftJoin(['doctor_specialty' => $this->getDoctorSpecialtiesQuery()], 'doctor_specialty.doctor_id = doctor.id')
			->leftJoin(['doctor_procedure' => $this->getDoctorProceduresQuery()], 'doctor_procedure.doctor_id = doctor.id')
			;
			
			$query->where(['or', 
				['like', '"user"."first_name" || \' \' || "user"."last_name"', $searchText],
				['like', 'doctor_specialty.specialties', $searchText],
				['like', 'doctor_procedure.procedures', $searchText]
			]);		
		}
		
		$count = null;
		if ($page == 1)
			$count = $query->count();
		
		$query->limit($limit)
		->offset($offset);
		
		//die($query->createCommand()->rawSql);
		
		$query_results = $query->all();
		$offices_list = [];
		foreach ($query_results as $query_result){
			$office = Office::findOne($query_result['office_id']);
			$doctor = $office->doctor;
			$available = true;
			if (count($doctor->getFirstAvailable($office->id, 1)) == 0){
				$available = false;
			}
			if ($office && $available)
				$offices_list[] = $office;
		}
		return ['data' => $offices_list, 'page' => $page, 'count' => $count];
	}
	
	public function getAverageSubquery(){
		$query = new Query();
		$plus = $this->getPlusQuery();
		$query->select([
			"CASE WHEN AVG(qualification.rate) IS NULL THEN 0+($plus) ELSE AVG(qualification.rate)+($plus) END AS average_rating"
		])
		->from('qualification')
		->where('qualification.doctor_id = doctor.id');
		return $query->createCommand()->rawSql;
	}
	
	public function getDoctorSpecialtiesQuery(){
		$lang = Yii::$app->language;
		$query = new Query();
		$query->select([
			"specialty2doctor.doctor_id AS doctor_id",	
			"STRING_AGG(specialty.name->>'$lang', ', ') AS specialties"
		])
		->from('specialty2doctor')
		->leftJoin('specialty', 'specialty2doctor.specialty_id = specialty.id')
		->groupBy('specialty2doctor.doctor_id');
		return $query;
	}
	
	public function getDoctorProceduresQuery(){
		$lang = Yii::$app->language;
		$query = new Query();
		$query->select([
				"procedure2doctor.doctor_id AS doctor_id",
				"STRING_AGG(procedure.name->>'$lang', ', ') AS procedures"
		])
		->from('procedure2doctor')
		->leftJoin('procedure', 'procedure2doctor.procedure_id = procedure.id')
		->groupBy('procedure2doctor.doctor_id');
		return $query;
	}
	
	public function getPlusQuery(){
		$query = new Query();
		$query->select([
			'CASE WHEN SUM(extra_rank) IS NULL THEN 0 ELSE SUM(extra_rank) END AS plus'
		])->from('membership2doctor')
		->innerJoin('membership', 'membership2doctor.membership_id = membership.id')
		->where('membership2doctor.doctor_id = doctor.id');
		return $query->createCommand()->rawSql;
	}
	
	public function getGeoLocation(){
		if (!$this->searchAddress)
			return null;
		
		$address = urlencode($this->searchAddress);
		$key = Yii::$app->params['apiGeocodeKey'];
		$url= "https://maps.googleapis.com/maps/api/geocode/json?address=$address\&key=$key";
		$options = [
				'ssl' => [
						'cafile' => Yii::getAlias('@common/jslibs/cacert/cacert.pem'),
						'verify_peer' => true,
						'verify_peer_name' => true,
				]
		];
		$location = file_get_contents($url, FILE_TEXT, stream_context_create($options));
		$location = json_decode($location, true);
		//die(json_encode($location));
		if ($location['status'] == 'OK'){
			return $location['results'][0]['geometry']['location']; // ['lat' => latitude, 'lng' => longitude]
		}
		else {
			return null;
		}
	}
	
}
