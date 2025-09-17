<?php

namespace App\Traits;
use App\Models\Gender;
use App\Models\CivilStatus;
use App\Models\DisabilityType;
use App\Models\LearnersClassification;
use App\Models\EmploymentStatus;
use App\Models\EducationalAttainment;
use App\Models\PrivancyConsent;
use App\Models\DisabilityCause;

trait EnrolleeTrait
 {

    public function getGender( $genderId ) {

        $genders = Gender::all();
        $string =  '<div>';
        foreach ( $genders as $gender ) {
            if ( $genderId === $gender->id ) {
                $string .=  '<img width=15 src='. $this->check . '/> '. $gender->name . '<br />';
            } else {
                $string .=  '<img width=15 src='. $this->unCheck . '/> '. $gender->name . '<br />';
            }
        }

        $string .= '</div>';
        return $string;
    }

    public function getCivilStatus( $civilStatusId ) {

        $civilStatuses = CivilStatus::all();
        $string =  '<div>';
        foreach ( $civilStatuses as $civilStatus ) {
            if ( $civilStatusId === $civilStatus->id ) {
                $string .=  '<img width=15 src='. $this->check . '/> '. $civilStatus->name . '<br />';
            } else {
                $string .=  '<img width=15 src='. $this->unCheck . '/> '. $civilStatus->name . '<br />';
            }
        }

        $string .= '</div>';
        return $string;
    }

    public function getEmploymentStatus( $employmentStatusId ) {
        $employmentStatuses = EmploymentStatus::all();
        $string =  '<div>';
        foreach ( $employmentStatuses as $employmentStatus ) {
            if ( $employmentStatusId === $employmentStatus->id ) {
                $string .=  '<img width=15 src='. $this->check . '/> '. $employmentStatus->name . '<br />';
            } else {
                $string .=  '<img width=15 src='. $this->unCheck . '/> '. $employmentStatus->name . '<br />';
            }
        }

        $string .= '</div>';
        return $string;
    }

    public function getEducationalAttainment( $educationalAttainmentId ) {
        $educationalAttainments = EducationalAttainment::all();
        $string = '<div  class="educationalChunk">';
        foreach ( $educationalAttainments->chunk( 5 ) as $key =>  $educationalAttainmentChunk ) {
            if ( $key === 1 ) {
                $string .=  '<span class="educationLargeWidth">';
            } else {
                $string .=  '<span>';
            }

            foreach ( $educationalAttainmentChunk as $educationalAttainment ) {

                if ( $educationalAttainmentId === $educationalAttainment->id ) {
                    $string .=  '<img width=15 src='. $this->check . '/> '. $educationalAttainment->name . '<br />';
                } else {
                    $string .=  '<img width=15 src='. $this->unCheck . '/> '. $educationalAttainment->name . '<br />';
                }
            }

            $string .= '</span>';

        }
        $string .= '</div>';

        return $string;
    }

    public function getLearnersClassification( $learnersClassificationId ) {
        $learnersClassifications = LearnersClassification::all();
        $string = '<div  class="learnersClassificationChunk">';
        $count = $learnersClassifications->chunk( 10 )->count() - 1;
        foreach ( $learnersClassifications->chunk( 10 ) as $key =>  $learnerClassificationChunk ) {
            if ( $key === 1 ) {
                $string .=  '<span class="learnersLargeWidth">';
            } else {
                $string .=  '<span>';
            }

            foreach ( $learnerClassificationChunk as $learnersClassification ) {

                if ( $learnersClassificationId === $learnersClassification->id ) {
                    $string .=  '<img width=15 src='. $this->check . '/> '. $learnersClassification->name . '<br />';
                } else {
                    $string .=  '<img width=15 src='. $this->unCheck . '/> '. $learnersClassification->name . '<br />';
                }
            }

            if ( $key === $count ) {
                $string .=  '<img width=15 src='. $this->unCheck . '/> Others:____________ <br />';
            }
            $string .= '</span>';

        }
        $string .= '</div>';

        return $string;
    }

    public function getDisablityType( $disabilityTypeId ) {

        $disabilityTypes = DisabilityType::all();
        $string =  '<div class="disabilityTypeDiv">';
        foreach ( $disabilityTypes as $disabilityType ) {
            if ( $disabilityTypeId === $disabilityType->id ) {
                $string .=  '<span class="disabilityType"><img class="disabilityImg" width=15 src='. $this->check . '/> '. $disabilityType->name . '</span>';
            } else {
                $string .=  '<span class="disabilityType"> <img class="disabilityImg" width=15 src='. $this->unCheck . '/> '. $disabilityType->name  . '</span>';
            }
        }

        $string .= '</div>';
        return $string;
    }

    public function getPrivacyConsent( $privacyConsentId ) {
        $privacyConsents = PrivancyConsent::all();
        $string =  '<div class="privancyConsentList">';
        foreach ( $privacyConsents as $privacyConsent ) {
            if ( $privacyConsentId === $privacyConsent->id ) {
                $string .=  '<img width=15 src='. $this->check . '/> '. $privacyConsent->name . '<span class="centerSpace"></span>';
            } else {
                $string .=  '<img width=15 src='. $this->unCheck . '/> '. $privacyConsent->name . '<span class="centerSpace"></span>';
            }
        }

        $string .= '</div>';
        return $string;
    }

    public function uliSpan( $string ) {
        $stringSplit = str_split( $string, 1 );
        $string = '';
        foreach ( $stringSplit as $character ) {
            $string .= '<span class="uliSpan">' . $character . '</span>';
        }
        return $string;
    }

    public function getDisabilityCauses($disabilityCauseId){

        $disabilityCauses = DisabilityCause::all();
        $string =  '<div class="disabilityTypeDiv">';
        foreach($disabilityCauses as $disabilityCause){
            if ( $disabilityCauseId === $disabilityCause->id ) {
                $string .=  '<span class="disabilityType"><img class="disabilityImg" width=15 src='. $this->check . '/> '. $disabilityCause->name . '</span>';
            } else {
                $string .=  '<span class="disabilityType"> <img class="disabilityImg" width=15 src='. $this->unCheck . '/> '. $disabilityCause->name  . '</span>';
            }
        }
        
        $string .= '</div>';
        return $string;
    }

}