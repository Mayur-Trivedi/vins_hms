<?php
  namespace euro_hms\Api\Repositories;


 use euro_hms\Models\PatientCheckUp;
 use euro_hms\Models\PatientDetailsForm;
 use Carbon\Carbon;
 use DB;
 use euro_hms\Api\Repositories\UserRepository;
 use euro_hms\Models\OpdDetails;
 use euro_hms\Models\IpdDetails;

 class PatientRepository 
 {

    public function __construct(){
        $this->patientDetailObj = new PatientDetailsForm();
    }

 	public function patient_add($request) 	
 	{ 
 		$data = $request->all()['patientData']['patientData'];
        $patientType = $request->all()['patientData']['patientType'];
        $a_time=$data['appointment_datetime']['time'];
 		    $uhid="VN";
        $year = date('y');
        $insertedPatientId="";
		
        if($data['case'] == 'new') {
        	$patientData=new PatientDetailsForm();

        }
        else
        {

        	$patientId =$data['patient_id'];
	          
        	if($patientId!=0 && $patientId!="")

        	{
        		$patientData=PatientDetailsForm::findOrFail($patientId);


        	}
        	else
        	{
        		return ['code' => '300','data'=>'', 'message' => 'Record not found'];
        	}
        	
        }
     
        /*patient details*/
    $patientData->first_name=$data['fname'];
		$patientData->middle_name=$data['mname'];
		$patientData->last_name=$data['lname'];
    $patientData->dob= $data['dob']['time'];
		$patientData->gender=$data['gender'];
    $patientData->age=$data['age'];
    $patientData->type=$data['type'];
		$patientData->address=$data['address'];
		$patientData->ph_no=$data['ph_no'];
		$patientData->mob_no=$data['mob_no'];
		$patientData->references=$data['reference_dr'];
		$patientData->consultant_id=$data['consulting_dr'];
		$patientData->consultant=$data['consulting_dr'];
		$patientData->case_type=$data['case'];
		$patientData->appointment_datetime=$data['appointment_datetime']['time']; 

		/*for patient details end*/


        if($data['case'] == 'new') {
           $patientD =  PatientDetailsForm::orderBy('id', 'desc')->first();
            if($patientD == null){   
                $lastPatientId = 1; 
            }else{  
                $lastPatientId = $patientD->id + 1;  
            }
           $newPatNo = sprintf("%04d",$lastPatientId);
           $insertedPatientId=$uhid.$year.$newPatNo;
			/*for patient details start*/
			$patientData->uhid_no=$insertedPatientId;
			$patientData->created_at=Carbon::now();
		    $patientData->updated_at=Carbon::now();
			$patientData->save();
			$patientId = $patientData->id;
			/*for patient details end*/
        } else {
	        /*for patient details start*/
    			$patientData->updated_at=Carbon::now();
    			$patientData->save();
    			/*for patient details end*/
        }

        if ($patientId) {
            if($patientType == "opd"){
            	
            	/*to get OPD Id*/
		    	$opd_prefix="OPD";
		        $opdId =  OpdDetails::orderBy('id', 'desc')->first();
		        if($opdId == null){   
		            $lastOPD = 1; 
		        }else{  
		            $lastOPD = $opdId->id + 1;  
		        }
		       	$newPatOPDNo = sprintf("%04d",$lastOPD);
		   		$insertedOPDId=$opd_prefix.$year.$newPatOPDNo;
                $caseData = OpdDetails::create([
                	'opd_id'=>$insertedOPDId,
                    'patient_id'=> $patientId,
                    'uhid_no'=> $patientData->uhid_no,
                    'admit_datetime' =>  Carbon::now(),
                    'appointment_datetime'=>$patientData->appointment_datetime 
                ]);

                if ($caseData) {
                    return ['code' => '200','data'=>['patientId'=> $patientId,'opdId' => $caseData->id,'uhid_no'=>$patientData->uhid_no], 'message' => 'Record Sucessfully created'];
                } else {
                    return ['code' => '400','data'=>'', 'message' => 'Something goes wrong'];
                }    
            }
            else{
            	$ipd_prefix="IPD";
        		$year = date('y');
		        $ipdId =  IpdDetails::orderBy('id', 'desc')->first();
		        if($ipdId == null){   
		            $lastIPD = 1; 
		        }else{  
		            $lastIPD = $ipdId->id + 1;  
		        }
		       	$newPatIPDNo = sprintf("%04d",$lastIPD);
           		$insertedIPDId=$ipd_prefix.$year.$newPatIPDNo;
              
                 $caseData = IpdDetails::create([
                 	'ipd_id'=>$insertedIPDId,
                    'patient_id'=> $patientId,
                    'uhid_no'=> $patientData->uhid_no,
                    'admit_datetime' =>  Carbon::now(),
                     'appointment_datetime'=>$patientData->appointment_datetime
                ]);
                /*for patient check up start*/
				/*for patient check up end*/
                if ($caseData) {
                    return ['code' => '200','data'=>['patientId'=> $patientId,'ipdId' => $caseData->id,'uhid_no'=>$patientData->uhid_no], 'message' => 'Record Sucessfully created'];
                } else {
                    return ['code' => '400','data'=>'', 'message' => 'Something goes wrong'];
                }
            }

        }
        return ['code' => '400','data'=>'', 'message' => 'Something goes wrong'];
 	}

	/**
	 * [getPatientDetailsById description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getPatientDetailsById($id)
 	{
 		$get_patient_details=PatientDetailsForm::where('id',$id)->first();
 		return $get_patient_details;
 	}

    /**
     * [getOPDIdByPatientId description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getOPDIdByPatientId($pid)
    {
        return OpdDetails::where('patient_id',$pid)->get();
    }
    /**
     * [getPatientListByConsultDr description]
     * @param  [type] $doctor  [description]
     * @param  [type] $section [description]
     * @return [type]          [description]
     */
    public function getPatientListByConsultDr($doctor,$section){
       $result = array();
       $patientList =PatientDetailsForm::where('consultant_id',$doctor)->get();
      foreach ($patientList as $key=>$value){
         $result[$key] = [
            'id' => $value->id,
            'name' =>  $value->first_name.' '.$value->last_name,
            'consultant' =>  $value->consultant_id,
            'dob' => $value->dob,
            'gender' =>  $value->gender,
            'address' =>  $value->address,
            'uhid_no' =>  $value->uhid_no
          ] ;
      }
      return  $result;
    }

    /**
     * [patientCheckUpDetailsByOpdId description]
     * @param  [type] $oid [description]
     * @return [type]      [description]
     */
    public function patientCheckUpDetailsByOpdId($oid)
    {
        return PatientCheckUp::where('opd_id',$oid)->first();
    }

   /**
    * [getPatientListBySearch description]
    * @param  [type] $request [description]
    * @return [type]          [description]
    */
    public function getPatientListBySearch($request)
    {
        $data = $request->all()['searchData'];
        $search_data=$request->all()['searchData']['search_data'];
        $user_id=$data['user_id'];

        $date="";
        if($search_data['select_type_dob']['time']!="")
          $date=Carbon::createFromFormat('d-m-Y', $search_data['select_type_dob']['time'])->format('Y-m-d');

        $patientList=array();
        $tes_query=0;
        
        if($search_data['name']!='' || $search_data['uhid_no']!='' || $date!='' || $search_data['mobile_no']!='')
        {

            $patientList= PatientDetailsForm::where(function ($query) use ($search_data,$date,$tes_query,$user_id) {
                if($user_id!=0 && $user_id!='' && $tes_query==0)
                {
                    $query->where('consultant_id',$user_id);
                    
                }
                $string = preg_replace('/\s+/',',',$search_data['name']);

                if($search_data['name']!='' && $tes_query==0)
                {
                   $query->where('first_name', 'like', '%'.$string.'%')
                      ->orWhereRaw("LOCATE (first_name, ?)", [$string])
                      ->orWhere('middle_name', 'like', '%'.$string.'%')
                      ->orWhereRaw("LOCATE (middle_name, ?)", [$string])
                      ->orWhere('last_name', 'like', '%'.$string.'%')
                      ->orWhereRaw("LOCATE (last_name, ?)", [$string]);
                      $tes_query=1;
                }
                else if($search_data['name']!='')
                {

                    $query->orWhere('first_name', 'like', '%'.$string.'%')
                       ->orWhereRaw("LOCATE (first_name, ?)", [$string])
                      ->orWhere('middle_name', 'like', '%'.$string.'%')
                      ->orWhereRaw("LOCATE (middle_name, ?)", [$string])
                      ->orWhere('last_name', 'like', '%'.$string.'%')
                      ->orWhereRaw("LOCATE (last_name, ?)", [$string]);
                }
                if($search_data['uhid_no']!=0 && $search_data['uhid_no']!='' && $tes_query==0)
                {

                    $query->where('uhid_no', 'like', '%'.$search_data['uhid_no'].'%');
                    $tes_query=1;
                }
                else if($search_data['uhid_no']!='' && $search_data['uhid_no']!=0)
                {

                    $query->orWhere('uhid_no', 'like', '%'.$search_data['uhid_no'].'%');
                }
                 if($date!='' && $tes_query==0)
                 {
                     $query->whereDate('dob', $date);
                     $tes_query=1;
                 }
                 else if($date!='')
                {
                     $query->orWhereRaw("DATE(dob) = ?", [$date]);
                } 
                 if($search_data['mobile_no']!=''  && $tes_query==0)   
                 {
                    $query->where('mob_no', 'like', '%'.$search_data['mobile_no'].'%');
                    $tes_query=1;
                 }
                 else if($search_data['mobile_no']!='')
                 {
                    $query->orWhere('mob_no', 'like', '%'.$search_data['mobile_no'].'%');
                 }
            })->get();
            //dd($patientList);
            
        }

        
        if(count($patientList)>0) {
             return ['code' => '200','data'=>$patientList, 'message' => 'Patient record '];
        } else {
             //return ['code' => '300','patientData'=>'', 'message' => 'Record not found'];
             return ['code' => '300','data'=>'', 'message' => 'Something went wrong'];
        }
    }

    public function addPatientCheckup($request){
        
            /*for patient details start*/
        $data = $request->all()['pData']['patientData'];
        $userId = $request->all()['pData']['userId'];
        $data_patient_checkup=new PatientCheckUp;
        $data_patient_checkup->user_id=$userId;
        $data_patient_checkup->height=$data['height'];
        $data_patient_checkup->weight=$data['weight'];
        $data_patient_checkup->bmi=$data['bmi'];
        $data_patient_checkup->vitals=$data['vitals'];
        $data_patient_checkup->pulse=$data['pulse'];
        $data_patient_checkup->bp=$data['bp_systolic'].'/'.$data['bp_diastolic'];
        $data_patient_checkup->temp=$data['temp'];
        $data_patient_checkup->pain=$data['pain_value'];
        $data_patient_checkup->opd_id=$data['opd_id'];
        $data_patient_checkup->patient_id=$data['patient_id'];
        $data_patient_checkup->created_at=Carbon::now();
        $data_patient_checkup->updated_at=Carbon::now();
        $data_patient_checkup->save();
        if ($data_patient_checkup) {
            return ['code' => '200','data'=>$data_patient_checkup, 'message' => 'Record Sucessfully created'];
        } else {
            return ['code' => '400','data'=>'', 'message' => 'Something goes wrong'];
        }
        /*to add data */
    }
 }
?>