<?php
namespace patients\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\daemons\WebsocketClient;
use common\models\LoginForm;
use common\models\User;
use patients\models\DoctorSignupForm;
use patients\models\PasswordResetRequestForm;
use patients\models\PatientSignupForm;
use patients\models\ResetPasswordForm;
use patients\models\ContactForm;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
            	'only' => ['logout', 'signup', 'activation-pending', 'account-disabled'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'activation-pending', 'account-disabled'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        	'auth' => [
        		'class' => 'yii\authclient\AuthAction',
        		'successCallback' => [$this, 'successCallback'],
        	],
        ];
    }

    public function successCallback($client)
    {
    	$attributes = $client->getUserAttributes();
    	// user login or signup comes here
    	/*
    	 Checking facebook email registered yet?
    	 Maxsure your registered email when login same with facebook email
    	 die(print_r($attributes));
    	 */

    	$user = User::find()->where(['email'=>$attributes['email']])->one();
    	if(!empty($user)){
    		if ($result = $this->loginUser($user))
    			return $result;
    	}else{
    		// Save session attribute user from FB
    		$session = Yii::$app->session;
    		$session['attributes']=$attributes;
    		// redirect to form signup, variabel global set to successUrl
    		$this->successUrl = \yii\helpers\Url::to(['signup']);
    	}
    }
    public $successUrl = 'Success';

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
    	if (Yii::$app->user->isGuest){
    		$this->layout = 'landing';
    	}

        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin($doctor_id=null, $office_id=null)
    {

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'landing';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        	$user = $model->user;
        	if ($result = $this->loginUser($user, $doctor_id, $office_id))
        		return $result;

        } else {
            return $this->render('login', [
                'model' => $model,
            	'doctor_id' => $doctor_id,
            	'office_id' => $office_id
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup($profileType=null, $doctor_id=null, $office_id=null)
    {
    	$this->layout = 'landing';

    	if ($profileType == User::DOCTOR)
        	$model = new DoctorSignupForm();
    	else
    		$model = new PatientSignupForm();

        if ($model->load(Yii::$app->request->post())) {
        	if ($user = $model->signup()) {
        		$user->sendConfirmationMail($doctor_id, $office_id);
        		return $this->loginUser($user);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }


    private function loginUser($user, $doctor_id=null, $office_id=null){
    	$token = $user->authKey;
    	switch ($user->profileType()){
    		case User::PATIENT:
    			if (Yii::$app->user->login($user)){
    				if ($doctor_id && $office_id)
    					return $this->redirect(['/doctors/profile', 'id' => $doctor_id, 'office_id' => $office_id]);
    				return $this->goBack();
    			}
    		case User::DOCTOR:
    			return $this->redirect(Yii::$app->params['doctorsDomain'].'/site/login?' . http_build_query(['username' => $user->username, 'token' => $token]));
    		case User::STAFF:
    			return $this->redirect(Yii::$app->params['adminDomain'].'/site/login?'. http_build_query(['username' => $user->username, 'token' => $token]));
    	}
    	return false;
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goBack();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionAccountDisabled(){
    	$user = Yii::$app->user->identity;
    	if ($user->status == User::STATUS_ACTIVE){
    		return $this->goHome();
    	}
    	return $this->render('account-disabled');
    }

    public function actionActivationPending(){
    	$user = Yii::$app->user->identity;
    	if ($user->status == User::STATUS_ACTIVE){
    		return $this->goHome();
    	}
    	if (Yii::$app->request->isPost){

    		/* @var \common\models\User $user */
    		$user->sendConfirmationMail();
    	}
    	return $this->render('activation-pending');

    }

    public function actionActivateAccount($token = '', $doctor_id = null, $office_id = null){

    	if (empty($token) || !is_string($token)) {
    		throw new BadRequestHttpException(Yii::t('app','The activation link is wrong.'));
    	}
    	$user = User::findByPasswordResetToken($token);
    	if (!$user) {
    		Yii::$app->session->setFlash('warning', Yii::t('app', 'Wrong or expired activation token. Please, log-in and resend the confirmation email'));
    		return $this->redirect('login');
    	}

    	if ($user->isPasswordResetTokenValid($token)){
	    	$user->status = User::STATUS_ACTIVE;
	    	$user->removePasswordResetToken();
	    	if ($user->save()){
	    		if (Yii::$app->user->isGuest || Yii::$app->user->identity->id != $user->id){
	    			return $this->loginUser($user, $doctor_id, $office_id);
	    		}
	    		if ($doctor_id && $office_id){
	    			return $this->redirect(['/doctors/profile', 'id' => $doctor_id, 'office_id' => $office_id]);
	    		}
	    		return $this->goHome();
	    	}
    	}
    	return $this->redirect('activation-pending');
    }

    public function actionSocketTest(){
    	Yii::$app->response->format = Response::FORMAT_JSON;
    	$entryData = ['action' => 'chat', 'message' => 'Hola Mundo'];

    	$WebSocketClient = new WebsocketClient('localhost', 8080);
    	$result = $WebSocketClient->sendData(json_encode($entryData));
    	unset($WebSocketClient);

    	return ['result' => $result];
    }
}
