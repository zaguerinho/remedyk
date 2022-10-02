<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Notification;
use common\models\User;

/**
 * NotificationSearch represents the model behind the search form about `common\models\Notification`.
 */
class NotificationSearch extends Notification
{
	
	/**
	 * R-Level filter
	 *
	 * @var string
	 */
	public $r_filter;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'target_id'], 'integer'],
            [['text', 'target_url', 'datetime', 'seen_at', 'visited_at', 'fa_icon_class'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Notification::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'sort'  => [
        		'defaultOrder' => [
        			'datetime' => SORT_DESC,
        		],
        	],
        ]);

        $user = User::getUserIdentity();
        $query->where(['target_id' => $user->id]);
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'datetime' => $this->datetime,
            'seen_at' => $this->seen_at,
            'visited_at' => $this->visited_at,
            'target_id' => $this->target_id,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'target_url', $this->target_url])
            ->andFilterWhere(['like', 'fa_icon_class', $this->fa_icon_class]);

            
            if(!empty($_REQUEST['NotificationSearch']) && !empty($_REQUEST['NotificationSearch']['r_filter'])){
            	$filter = trim($_REQUEST['NotificationSearch']['r_filter']);
            	
            	$query->andWhere('CONCAT(text, target_url) ILIKE \'%' . $filter . '%\'');
            	
            	
            	
            }
            
        return $dataProvider;
    }
}
