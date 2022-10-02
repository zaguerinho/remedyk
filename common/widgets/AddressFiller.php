<?php
namespace common\widgets;

use common\assets\AddressAsset;
use Yii;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;

class AddressFiller extends Widget {
	public function init(){
		parent::init();
		$view = $this->getView();
		
		$address = $this->options['address'];
		$form = $this->options['form'];
		$id = $this->options['id'];
		echo $this->getHtml($address, $form, $id);
		
		AddressAsset::register($view);
		$view->registerJsFile(Yii::$app->params['urlMaps'],
			[
				'depends' => [AddressAsset::className()],
				'async' => true,
				'defer' => true
			]);
	}
	
	public function getHtml($address, $form, $id){
			return '<fieldset id="address-form" style="border: 1px solid #D2D2D2; background-color: #F8F8F8;">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-xs-12">
								<div id="locationField" class="form-group"  style="background-color: #FFFFFF">
									<label class="control-label" for="autocomplete">'.Yii::t('app', 'Write full address...').'</label>'
				                 	.Html::textInput($id,'',['id' => $id, 'class'=>'autocomplete form-control input-lg']).
				                	'<small>'.Yii::t('app', 'Search on ') .
				                	'<span>G</span><span>o</span><span>o</span><span>g</span><span>l</span><span>e</span>?</small>
				            </div>
			            </div>
			        </div>
			        <div class="separator"></div>
			        <div class="row">
			            <div class="col-md-12">
			                '.$form->field($address, 'route')
			                		->textInput(['id' => $id.'_route', 'class' => 'form-control input-sm']).
			            '</div>
			
			        </div>
			        <div class="row">
			            <div class="col-md-8 col-sm-12">'
			                    .$form->field($address, 'number')
			                    		->textInput(['id' => $id.'_street_number', 'class' => 'form-control input-sm'])
			                    		->label(Yii::t('app', 'Nº')).
			            '</div>
			            <div class="col-md-4 col-sm-12">'
			                .$form->field($address, 'postal_code')
			                		->textInput(['id' => $id.'_postal_code', 'class' => 'form-control input-sm']).
			            '</div>
			        </div>
			        <div class="row">
			            <div class="col-md-6 col-sm-12">'
			                .$form->field($address, 'locality')
			                		->textInput(['id' => $id.'_locality', 'readonly' => true, 'class' => 'form-control input-sm']).
			            '</div>
			            <div class="col-md-6 col-sm-12">'
			            		.$form->field($address, 'administrative_area_level_3')
			            		->textInput(['id' => $id.'_administrative_area_level_2', 'readonly' => true, 'class' => 'form-control input-sm'])
			                		->label(Yii::t('app', 'Province')).
			            '</div>
			        </div>
			        <div class="row">
			            <div class="col-md-6 col-sm-12">'
			            		.$form->field($address, 'administrative_area_level_1')
			            		->textInput(['id' => $id.'_administrative_area_level_1', 'readonly' => true, 'class' => 'form-control input-sm'])
			                		->label(Yii::t('app', 'State')).
			            '</div>
			            <div class="col-md-6 col-sm-12">'
			                .$form->field($address, 'country')
			                ->textInput(['id' => $id.'_country', 'readonly' => true, 'class' => 'form-control input-sm']).
			            '</div>
			        </div>'
			        .$form->field($address, 'lat')
			        ->hiddenInput(['id' => $id.'_lat'])
			        		->label(false)
			        .$form->field($address, 'lng')
			        ->hiddenInput(['id' => $id.'_lng'])
			        		->label(false)
			        .$form->field($address, 'url_gmaps')
			        ->hiddenInput(['id' => $id.'_url_gmaps'])
			        		->label(false).
			    '</div>
			</div>
		</fieldset>';
	}
}