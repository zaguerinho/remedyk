
<div class="outline">
	<div class="left" id="logo-wrapper">
		<h3><img class="logo" src="/images/logo.png">Remedyk</h3>
	</div> 
	<div class="left" id="title">
		<h2><?= Yii::t('app', 'PRESCRIPTION') ?></h2>
	</div>
	<div class="right" id="prescription-number"><!-- Prescription number -->
		<strong><?= Yii::t('app', 'Id:') ?></strong> # {id}
	</div>
	<div class="clearfix"></div>
	<div class="right" id="issue-date-time">
		<strong><?= Yii::t('app', 'Issued on:') ?></strong> {datetime}
	</div>
	<div class="separator"></div>
	
	<div class="left default-margins" id="patient-name">
		<strong><?= Yii::t('app', 'Patient Name:') ?></strong><br>{patient_name}
	</div>	
	<div class="clearfix"></div>
	
	<div class="left default-margins" id="patient-gender">
		<strong><?= Yii::t('app', 'Gender:') ?></strong><br>{patient_gender}
	</div>
	<div class="left default-margins" id="patient-age">
		<strong><?= Yii::t('app', 'Age:') ?></strong><br>{patient_age}
	</div>
	<div class="left default-margins" id="patient-weight">
		<strong><?= Yii::t('app', 'Weight:') ?></strong><br>{patient_weight}
	</div>
	<div class="left default-margins" id="patient-height">
		<strong><?= Yii::t('app', 'Height:') ?></strong><br>{patient_height}
	</div>
	<div class="left default-margins" id="patient-blood">
		<strong><?= Yii::t('app', 'Blood type:') ?></strong><br>{patient_blood}
	</div>
	<div class="clearfix"></div>
	
	<div class="left default-margins" id="patient-email">
		<strong><?= Yii::t('app', 'Email:') ?></strong><br>{patient_email}
	</div>
	<div class="left default-margins" id="patient-phone">
		<strong><?= Yii::t('app', 'Phone:') ?></strong><br>{patient_phone}
	</div>
	<div class="clearfix"></div>
	
	<div class="left default-margins" id="patient-address">
		<strong><?= Yii::t('app', 'Address:') ?></strong><br>{patient_address}
	</div>
	<div class="clearfix"></div>
	<table>
		<tr>
			<th id="medicine"><?= Yii::t('app', 'Medicine') ?></th>
			<th id="quantity"><?= Yii::t('app', 'Quantity') ?></th>
			<th id="freq-lapse"><?= Yii::t('app', 'Frequency/Lapse') ?></th>
			<th id="notes"><?= Yii::t('app', 'Notes') ?></th>
		</tr>
		<tbody class="repeat" id="prescription_details">
			<tr>
				<td>{medicine_name}</td>
				<td>{quantity}</td>
				<td>{frequency} / {lapse}</td>
				<td>{notes}</td>
			</tr>
		</tbody>
	</table>
	
	<div class="left default-margins" id="general-notes">
		<strong><?= Yii::t('app', 'General Notes:') ?></strong><br>{general_notes}
	</div>
	<div class="separator"></div>
	
	<div class="left default-margins" id="doctor-name">
		<strong><?= Yii::t('app', 'Doctor Name:') ?></strong><br>{doctor_name}
	</div>
	<div class="left default-margins" id="doctor-license">
		<strong><?= Yii::t('app', 'Proffessional License:') ?></strong><br>{doctor_license}
	</div>
	<div class="clearfix"></div>
	
	<div class="left default-margins" id="doctor-signature">
		<strong>
			_____________________________________________<br>
			<?= Yii::t('app', 'Doctor\'s Signature') ?>
			
		</strong>
	</div>
	
	<div class="left default-margins" id="reminder">
		<strong>
			
			<?= Yii::t('app', 'NOT VALID TO PRESCRIPT MEDICINES FROM GROUP I.') ?>
			
		</strong>
	</div>
</div>