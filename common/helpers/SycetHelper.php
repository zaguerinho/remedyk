<?php
namespace common\helpers;

use common\models\Commission;
use common\models\Membership2doctor;

class SycetHelper {
	
	/**
	 * Creates a CFDI using Sycet, stamp it and send it by email.
	 * @param Commission $commission
	 */
	public static function createCfdiForPatient($commission){
		return true;
	}
	
	/**
	 * Creates a CFDI using Sycet, stamp it and send it by email.
	 * @param Membership2doctor $membership2doctor
	 */
	public static function createCfdiForDoctor($membership2doctor){
		return true;
	}
	
	
}