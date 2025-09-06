<?php

namespace App\Http\Controllers;

use App\Repositories\Enrollee\EnrolleeRepository;
use App\Http\Requests\BaseIndexRequest;
use App\Models\Enrollee;
use Illuminate\Support\Arr;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Dompdf\Options;

use App\Traits\EnrolleeTrait;

use Carbon\Carbon;

class EnrolleeController extends ApiController {

    use OptimusRequiredToModel, EnrolleeTrait;

    public $check;
    public $unCheck;

    public function __construct( EnrolleeRepository $repository ) {
        $this->model =  Enrollee::class;
        $this->repository = $repository;
        $this->indexRequest = BaseIndexRequest::class;
        $this->updateRequest = BaseIndexRequest::class;
        $this->check = url('http://localhost:9000/check.png');
        $this->unCheck = url('http://localhost:9000/uncheck.jpg');
    }

    public function isPublicRoute( string $routeName ): Bool {
        return true;
    }

    public function store() {
        $newRequest = $this->newRequest();
        $enrollee = Enrollee::create( $newRequest );
        return response()->json( [
            'data' => Enrollee::find( $enrollee->id )
        ] );

    }

    public function update( $id ) {
        $newRequest = $this->newRequest();
        $enrollee = Enrollee::where( 'id', $this->optimus()->decode( $id ) )->first();
        if ( $enrollee ) {
            $enrollee->update( $newRequest );
        }
        return response()->json( [
            'data' => Enrollee::find( $this->optimus()->decode( $id ) )
        ] );

    }

    public function newRequest() {
        $request = app()->make( 'request' );
        $newRequest = $request->all();
        $newRequest[ 'schedule_id' ] = Arr::get( $request->schedule, 'id' );
        $newRequest[ 'gender_id' ] = Arr::get( $request->gender, 'id' );
        $newRequest[ 'civil_status_id' ] = Arr::get( $request->civil_status, 'id' );
        $newRequest[ 'employment_status_id' ] = Arr::get( $request->employment_status, 'id' );
        $newRequest[ 'birthday_province_id' ] = Arr::get( $request->birthday_province, 'id' );
        $newRequest[ 'birthday_city_id' ] = Arr::get( $request->birthday_city, 'id' );
        $newRequest[ 'courses_classification_id' ] = Arr::get( $request->courses_classification, 'id' );
        $newRequest[ 'educational_attainment_id' ] = Arr::get( $request->educational_attainment, 'id' );
        $newRequest[ 'salary_id' ] = Arr::get( $request->salary, 'id' );

        $newRequest[ 'permanent_city_id' ] = Arr::get( $request->permanent_city, 'id' );
        $newRequest[ 'permanent_province_id' ] = Arr::get( $request->permanent_province, 'id' );
        $newRequest[ 'voucher_course_qualification_id' ] = Arr::get( $request->voucher_course_qualification, 'id' );
        $newRequest[ 'workers_classification_id' ] = Arr::get( $request->workers_classification, 'id' );
        return $newRequest;
    }

    public function attachUser() {
        $request = app()->make( 'request' );
        if ( $request->user_id == null ) {
            $enrollee =  Enrollee::where( 'id', $request->enrollee_id )->first();
            if ( $enrollee ) {
                $enrollee->update( [
                    'user_id' => null,
                    'is_enrollee_default' => true
                ] );
            }
        } else {
            $enrollee =  Enrollee::where( 'id', $request->enrollee_id )->first();
            if ( $enrollee ) {
                $enrollee->update( [
                    'user_id' => $request->user_id,
                    'is_enrollee_default' => true
                ] );
            }
        }

    }

    public function print( $id ) {
        $enrollee = Enrollee::where( 'id', $this->optimus()->decode( $id ) )->first();

        $images = $enrollee->images;
        $imgPathUrl = '';
        foreach($enrollee->images as $key => $img){
            if($key === 0){
                $imgPathUrl = $img->path_url;
            }
        }
        $imgPathUrl = str_replace(env('APP_URL'), '', $imgPathUrl);
        $uli = $this->uliSpan($enrollee->uli);
        $numberStreet = $enrollee->number_street;
        $brgy = $enrollee->brgy;
        $district = $enrollee->district;
        $permanentProvince = $enrollee->permanentProvince ? $enrollee->permanentProvince->name : '';
        $permanentCity = $enrollee->permanentCity ? $enrollee->permanentCity->name : '';
        $permanentEmail = $enrollee->permanent_email;
        $permanentContactNo = $enrollee->permanent_contact_no;
        $permanentRegion = $enrollee->permanent_region;

        $birthMonth = Carbon::parse($enrollee->birthday)->month;
        $birthDay = Carbon::parse($enrollee->birthday)->day;
        $birthYear = Carbon::parse($enrollee->birthday)->year;

        $parentName = $enrollee->parent_name;
        $parentCompleteMailing = $enrollee->parent_complete_mailing;

        $gender = $this->getGender($enrollee->gender_id);
        $civilStatus = $this->getCivilStatus($enrollee->civil_status_id);
        $employmentStatus = $this->getEmploymentStatus($enrollee->employment_status_id);
        $educationalAttainment = $this->getEducationalAttainment($enrollee->educational_attainment_id);
        $learnersClassification = $this->getLearnersClassification(1);
        $disabilityType = $this->getDisablityType($enrollee->disability_type_id);
        $disabilityCause = $this->getDisabilityCauses($enrollee->disability_cause_id);
        $privacyConsent = $this->getPrivacyConsent(1);
        $course = '';
        if($enrollee->coursesClassification){
            $course = $enrollee->coursesClassification->name;
        }
        $programEnrolled = $enrollee->program_enrolled;
        $scholarPackage = $enrollee->scholar_package;

        $entryDate = $enrollee->created_at->format( 'm/d/Y' );
        $lastName = $enrollee->lastname;
        $firstName = $enrollee->firstname;
        $middleName = $enrollee->middlename;

        $pdf = \App::make( 'dompdf.wrapper' );
        $pdf->setPaper( 'legal', 'portrait' );
        $pdf->loadHTML( "
            <style>
                .uliSpan {
                    padding-top: 8px;
                    margin-top: 10px;
                    display: inline-block;
                    border-left: 1px solid black;
                    border-top: 1px solid black;
                    border-bottom: 1px solid black;
                    width: 25px;
                    height: 30px;
                    text-align: center;
                }
                .uliSpan:last-child {
                     border-right: 1px solid black;
                }


                *{
                    font-family: serif;
                }
                body{
                    font-size: 14px;
                    font-family: Arial, Helvetica, serif;
                }
                table {
                    border: 1px solid black;
                    width: 100%;
                    border-collapse: collapse;
                   
                }

                .header-side-left{
                    width: 65px;
                }
                .header-side-right{
                    width: 25px;
                }

                tr, td, th {
                    margin: 0;
                    border: 1px solid black;
                }

                td {
                    padding: 5px;
                }

                .regForm {
                    font-size: 34px;
                    text-align: center;
                    padding: 0;
                    margin: 0;
                    font-weight: bold;
                }

                .pictureSide {
                    width: 120px;
                    margin: 3px;
                    height: 100px;
                    text-align: center;
                }

                .learnersProfile{
                    font-family: sans-serif;
                    text-align: center;
                }

                .parentName{
                    position: relative;
                    padding-top: 20px;
                }
                .name {
                    display: inline-block;
                    position: absolute;
                    top: 10px;

                }

                .lastname{
                    margin-left: 60px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 210px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }
                .firstname, .middlename {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 160px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }
                
                .lastname::before{
                    content: 'Last Name, Extension Name (Jr., Sr.)';
                    position: absolute;
                    top: 27px;
                }

                .firstname::before{
                    content: 'First';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .middlename::before{
                    content: 'Middle';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .completePermanent{
                    width: 120px;
                    display: inline-block;
                    position: absolute;
                    top: 10px;
                }
                
                .numberStreet{
                    margin-left: 125px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 210px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    height: 100px;
                }
                .numberStreet::before{
                    content: 'Number, Street';
                    position: absolute;
                    top: 110px;
                    left: 60px;
                }

                .brgy, .district {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 160px;
                    padding: 5px;
                    position: relative;
                    height: 100px;
                }

                .brgy::before{
                    content: 'Barangay';
                    position: absolute;
                    top: 110px;
                    left: 60px;
                }
                .district::before{
                    content: 'District';
                    position: absolute;
                    top: 110px;
                    left: 60px;
                }

                .city{
                    margin-left: 125px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 210px;
                    padding: 5px;
                    position: relative;
                    margin-bottom: 15px;
                }
                .province, .region {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 160px;
                    padding: 5px;
                    position: relative;
                    margin-bottom: 15px;
                }
                
                .city::before{
                    content: 'City/Municipality';
                    position: absolute;
                    top: 27px;
                }

                .province::before{
                    content: 'Province';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .region::before{
                    content: 'Region';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .fbAccount{
                    margin-left: 125px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 210px;
                    padding: 5px;
                    position: relative;
                    margin-bottom: 15px;
                }
                .contactNo, .nationality {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 160px;
                    padding: 5px;
                    position: relative;
                    margin-bottom: 15px;
                }
                
                .fbAccount::before{
                    content: 'Email Address/Facebook Account: ';
                    position: absolute;
                    top: 27px;
                }

                .contactNo::before{
                    content: 'Contact No:';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .nationality::before{
                    content: 'Nationality';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .gender {
                    width: 10px;
                    margin-top: 10px;
                }

                .monthOfBirth{
                    margin-left: 125px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 150px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }
                .dayOfBirth, .yearOfBirth {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 120px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }

                .age {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 120px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }
                
                .monthOfBirth::before{
                    content: 'Month of birth';
                    position: absolute;
                    top: 27px;
                }

                .dayOfBirth::before{
                    content: 'Day of birth';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .yearOfBirth::before{
                    content: 'Year of birth';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .birthCity{
                    margin-left: 125px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 200px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }
                .birthProvince, .birthRegion {
                    margin-left: 5px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 160px;
                    padding: 5px;
                    margin-top: 20px;
                    position: relative;
                    margin-bottom: 15px;
                }

                
                .birthCity::before{
                    content: 'City/Municipality';
                    position: absolute;
                    top: 27px;
                }

                .birthProvince::before{
                    content: 'Province';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }

                .birthRegion::before{
                    content: 'Region';
                    position: absolute;
                    top: 27px;
                    left: 60px;
                }
                .educationalChunk span {
                    margin-top: 30px;
                    display: inline-block;
                    width: 200px;
                }

                .educationLargeWidth{
                    width: 300px !important;
                }

                .guardianName{
                    margin-left: 125px;
                    border: 1px solid black;
                    display: inline-block;
                    width: 240px;
                    padding: 5px;
                    margin-top: 5px;
                    position: relative;
                    margin-bottom: 15px;
                }

                .guardianName::before{
                    content: 'Name';
                    position: absolute;
                    top: 27px;
                }
                .parentAddress{
                    border: 1px solid black;
                    display: inline-block;
                    width: 220px;
                    padding: 5px;
                    margin-top: 5px;
                    position: relative;
                    margin-bottom: 15px;
                }

                .parentAddress::before{
                    content: 'Complete Permanent Mailing Address';
                    position: absolute;
                    top: 27px;
                }

                .learnersClassificationChunk span {
                    margin-top: 60px;
                    display: inline-block;
                    width: 200px;
                }

                .learnersLargeWidth{
                    width: 300px !important;
                }

                .disabilityType {
                    display: inline-block;
                    width: 220px;
                }

                .disabilityImg{
                    display: inline-block;

                }
                .disabilityTypeDiv{
                    margin-top: 5px;
                }

                .mainTd{
                    font-size: 16px;
                    font-weight: bold;
                }

                .subTd {
                    font-weight: 500;
                }
                .blankTd {
                    height: 25px;
                }
                .certify p {
                    text-align: center;
                }

                .signatureSpan1{
                    display: inline-block;
                    width: 355px;
                    border-bottom: 1px solid black;
                }
                .signatureSpan2{
                    display: inline-block;
                    width: 155px;
                    border-bottom: 1px solid black;
                }
                .signatureSpan3{
                    text-align: center;
                    display: inline-block;
                    border: 1px solid black;
                    padding: 10px;
                    height: 110px;
                    width: 110px;
                    margin-top: 10px;

                }
                
                .signatureTd {
                    position: relative;
                    margin-left: 20px;
                    display: inline-block;
                }

                .signatureTd2{
                    position: relative;
                    margin-left: 20px;
                    display: inline-block;
                }
                .signatureSpan1::before {
                    content: 'APPLICANT`S SIGNATURE OVER PRINTED NAME';
                    top: 2px;
                    position: absolute;
                }
                .signatureSpan2::before {
                    content: 'DATE ACCOMPLISHED';
                    top: 2px;
                    position: absolute;
                }

                .signatureSpan11::before {
                    content: 'REGISTRAR/SCHOOL ADMINISTRATOR';
                    top: 2px;
                    position: absolute;
                }
                .signatureSpan22::before {
                    content: 'DATE RECEIVED';
                    top: 2px;
                    position: absolute;
                }

                 .signatureSpan11{
                    display: inline-block;
                    width: 355px;
                    border-bottom: 1px solid black;
                }
                .signatureSpan22{
                    display: inline-block;
                    width: 155px;
                    border-bottom: 1px solid black;
                }
                .signatureSpan33{
                    text-align: center;
                    display: inline-block;
                    border: 1px solid black;
                    padding: 10px;
                    height: 110px;
                    width: 110px;
                    margin-top: 10px;

                }
                .signatureSpan33::before{
                    content: 'Right thumbmark';
                    position: absolute;
                    top: 140px;
                    font-weight: bold;

                }
                .notedBy {
                  position: absolute;
                  top: -50px;
                }

                .overPrintedName{
                    position: absolute;
                    font-size: 10px;
                    top: 20px;
                    left: 60px;
                }
                .privancyConsentList{
                    margin-top: 10px;
                    text-align: center;
                }

                .centerSpace{
                    display: inline-block;
                    width: 100px;
                }
                

            </style>
            
            <table>
                <tr>
                    <th class='header-side-left'>
                        <img src='images/tesda.png' width='110'/>
                    </th>
                    <th>Technical Education and Skills Development Authority <br />
                    Pangasiwaan sa Edukasyong Teknikal at Pagpapaunlad ng Kasanayan 
                    </th>
                    <th  class='header-side-right' >
                        <div>
                            MIS 03-01 <br />
                            (ver. 2021)
                        </div>
                    
                    </th>
                </tr>
                <tr>
                    <td colspan='3'>
                        <p class='regForm'>Registration Form</p>
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <h1 class='learnersProfile'>LEARNERS PROFILE FORM </h1>
                    </td>
                    <td>
                        <div  class='pictureSide'>
                            <img src='$imgPathUrl' width='110'/>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                    1. T2MIS Auto Generated
                    </td>
                </tr>
                <tr>
                    <td colspan='2' class='subTd uliNumber'>
                        1.1. Unique Learner Identifier (ULI) Number: $uli
                    </td>
                    
                    <td>
                        <span class='subTd'>1.2. Entry Date: </span> $entryDate
                    </td>
                </tr>

                <tr>
                    <td colspan='3' class='mainTd'>
                    2. Learner/Manpower Profile 
                    </td>
                </tr>

                <tr>
                    <td  colspan='3' class='parentName'>
                    <span class='name' class='subTd'> 2.1 Name: </span>  <span class='lastname'>$lastName </span> <span class='firstname'> $firstName </span> <span class='middlename'> $middleName </span>
                    </td>
                </tr>
                

                <tr>
                    <td colspan='3' >
                        <div class='parentName'>
                            <span class='completePermanent subTd'>
                                2.2 Complete Permanent
                                Mailing Address:
                            </span>
                            <span class='numberStreet'>$numberStreet </span> <span class='brgy'> $brgy </span> <span class='district'> $district </span>
                
                        <div>
                        <div class='parentName'>
                            <span class='name'> </span>  <span class='city'>$permanentCity </span> <span class='province'> $permanentProvince </span> <span class='region'> $permanentRegion </span>
                        </div>
                        <div class='parentName'>
                            <span class='name'> </span>  <span class='fbAccount'>$permanentEmail </span> <span class='contactNo'> $permanentContactNo </span> <span class='nationality'> $middleName </span>
                        </div>

                    </td>
                
                </tr>
               
                <tr>
                    <td colspan='3' class='mainTd'>
                    3. Personal Information 
                    </td>
                </td>
                <tr>
                    <td class='subTd'>
                        3.1 Sex 
                    </td>
                    <td class='subTd'>
                        3.2. Civil Status
                    </td>
                    <td class='subTd'>
                        3.3 Employment (before the training) 
                    </td>
                </tr>
                <tr>
                    <td>
                        $gender
                    </td>
                    <td>
                        $civilStatus
                    </td>
                    <td>
                        $employmentStatus
                    </td>
                </tr>
                <tr>
                    <td  colspan='3' class='parentName'>
                        <span class='name subTd'> 3.4 Birthdate: </span>  <span class='monthOfBirth'>$birthMonth </span> <span class='dayOfBirth'> $birthDay </span> <span class='yearOfBirth'> $birthYear </span>
                        <span class='age'> $middleName </span>
                    </td>
                </tr>
                <tr>
                    <td  colspan='3' class='parentName'>
                        <span class='name subTd'> 3.5 Birthplace: </span>  <span class='birthCity'>$lastName </span> <span class='birthProvince'> $firstName </span> <span class='birthRegion'> $middleName </span>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                        3.6 Educational Attainment Before the Training (Trainee)
                    </td>
                </tr>
                <tr>
                    <td colspan='3'>
                        $educationalAttainment
                    </td>
                </tr>
                 <tr>
                    <td  colspan='3' class='parentName'>
                        <span class='name subTd'> 3.7 Parent/Guardian </span>  <span class='guardianName'>$parentName </span> <span class='parentAddress'> $parentCompleteMailing </span>
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                        4. Learner/Trainee/Student (Clients) Classification:
                    </td>
                </tr>
                <tr>
                    <td colspan='3'>
                        $learnersClassification
                    </td>
                </tr>
                
                <tr>
                    <td colspan='3' class='mainTd'>
                    5. Type of Disability (for Persons with Disability Only): To be filled up by the TESDA personnel
                    </td>
                </tr>
                <tr>
                    <td colspan='3'>
                        $disabilityType
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                    6. Causes of Disability (for Persons with Disability Only): To be filled up by the TESDA personnel
                    </td>
                </tr>
                <tr>
                    <td colspan='3'>
                            $disabilityCause
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                    7. Name of Course/Qualification
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='blankTd'>
                            $course / $programEnrolled
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                    8. If Scholar, What Type of Scholarship Package (TWSP, PESFA, STEP, others)? 
                    </td>
                </tr>
                <tr>
                    <td colspan='3'>
                            $scholarPackage 
                    </td>
                </tr>
                <tr>
                    <td colspan='3' class='mainTd'>
                    9. Privacy Consent and Disclaimer
                    </td>
                </tr>
                <tr>
                    <td colspan='3'>
                    I hereby attest that / have read and understood the Privacy Notice of TESDA through its website (https://www.tesda.gov.ph)
                    and thereby giving my consent in the processing of my personal information indicated in this Learners Profile. The
                    processing includes scholarships, employment, survey, and all other related TESDA programs that may be beneficial to my
                    qualifications. 
                    <br />
                    $privacyConsent
                    </td>
                </tr>

                <tr>
                   <td colspan='3' class='mainTd'>
                   10. Applicant's Signature 
                   </td>
                </tr>
                <tr>
                    <td colspan='3' class='certify'>
                            <p>This is to certify that the information stated above is true and correct.</p>
                            <br />
                            <div  class='signatureTd'>
                                <span class='signatureSpan1'></span>
                            </div>
                            <div  class='signatureTd'>
                                <span class='signatureSpan2'></span>
                            </div>
                            <div  class='signatureTd signature3'>
                                <span class='signatureSpan3'>
                                lx1 picture taken <br />
                                within the last 6 <br /> months
                                </span>
                            </div>

                             <div  class='signatureTd2'>
                                <span class='notedBy'>Noted by:</span>
                                <span class='overPrintedName'>(Signature Over Printed Name)</span>
                                <span class='signatureSpan11'></span>
                                

                            </div>
                            <div  class='signatureTd2'>
                                <span class='signatureSpan22'></span>
                            </div>
                            <div  class='signatureTd2'>
                                <span class='signatureSpan33'>
                                
                                </span>
                            </div>
                    </td>
                </tr>
            

            </table>" );
        return $pdf->stream();

    }


    
}
