<?php

namespace patients\controllers;

use common\models\SearchForm;
use Yii;


class SearchController extends \yii\web\Controller
{
	
    public function actionIndex()
    {
    	    	
    	$search = new SearchForm();
    	$get = Yii::$app->request->get();
    	
    	$results = $search->search($get);
    	
        return $this->render('index', ['results' => $results, 'search' => $search]);
    }

}
