<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Obfuscate\OptimusRequiredToModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
class Enrollee extends Model {
    use HasFactory, OptimusRequiredToModel;

    protected $table = 'enrollees';
    protected $fillable = [
      'schedule_id', 'mothers_name', 'birthday', 'mothers_occupation', 'mothers_home_address', 'mothers_contact_number', 'fathers_occupation', 'fathers_name', 'fathers_home_address', 'fathers_contact_number', 'fathers_occupation', 'employment_reason_leaving_job', 'middlename', 'lastname', 'firstname', 'employment_company_name', 'employment_position', 'employment_address', 'salary', 'employment_reason_leaving_id', 'employment_date_employed', 'employment_salary_id', 'program_enrolled', 'start_training_duration', 'end_training_duration', 'vocational_year_graduated', 'vocational_name', 'vocational_address', 'college_year', 'college_name', 'college_address', 'highschool_year', 'highschool_name', 'highschool_address', 'elementary_year', 'elementary_name', 'elementary_address', 'region', 'courses_classification_id', 'entry_date', 'educational_attainment_id', 'courses_classification_id', 'birthday_city_id', 'birthday_province_id', 'uli', 'entry_date', 'gender_id', 'civil_status_id', 'employment_status_id', 'brgy', 'district', 'number_street', 'permanent_city_id', 'permanent_province_id', 'permanent_contact_no', 'permanent_email', 'permanent_nationality', 'permanent_region', 'name_emergency', 'address_emergency', 'relationship_emergency', 'contact_number_emergency', 'voucher_number', 'scholar_package', 'voucher_course_qualification_id', 'is_enrollee_default', 'user_id', 'workers_classification_id', 'parent_name', 'parent_complete_mailing', 'privancy_consent_id', 'disability_type_id', 'disability_cause_id'
    ];
    protected $appends = [ 'optimus_id', 'age', 'label', 'value'];

    public function gender():HasOne {
        return $this->hasOne( Gender::class, 'id', 'gender_id' );
    }

    public function civilStatus():HasOne {
        return $this->hasOne( CivilStatus::class, 'id', 'civil_status_id' );
    }

    public function employmentStatus():HasOne {
        return $this->hasOne( EmploymentStatus::class, 'id', 'employment_status_id' );
    }

    public function user():HasOne {
        return $this->hasOne( User::class, 'id', 'user_id' );
    }


    public function birthdayProvince():HasOne {
        return $this->hasOne( Province::class, 'id', 'birthday_province_id' );
    }

    public function birthdayCity():HasOne {
        return $this->hasOne( Municipality::class, 'id', 'birthday_city_id' );
    }

    public function permanentProvince():HasOne {
        return $this->hasOne( Province::class, 'id', 'permanent_province_id' );
    }

    public function permanentCity():HasOne {
        return $this->hasOne( Municipality::class, 'id', 'permanent_city_id' );
    }

    public function getAgeAttribute(){
        return Carbon::parse($this->birthday)->age;
    }

    public function educationalAttainment():HasOne {
        return $this->hasOne( EducationalAttainment::class, 'id', 'educational_attainment_id' );
    }

    public function coursesClassification():HasOne {
        return $this->hasOne( CoursesClassification::class, 'id', 'courses_classification_id' );
    }

    public function voucherCourseQualification():HasOne {
        return $this->hasOne( CoursesClassification::class, 'id', 'voucher_course_qualification_id' );
    }

    public function employmentSalary():HasOne {
        return $this->hasOne( WorkersClassification::class, 'id', 'employment_salary_id' );
    }

    public function workersClassification():HasOne {
        return $this->hasOne( WorkersClassification::class, 'id', 'workers_classification_id' );
    }

    public function schedule():HasOne {
        return $this->hasOne( Schedule::class, 'id', 'schedule_id' );
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id')->orderBy('id', 'desc');
    }

    public function getLabelAttribute(){

        $schedule = Schedule::find($this->schedule_id);

        $scheduleName = $schedule ? $schedule->name : '';
       
        return $this->lastname . ', ' . $this->firstname . '(' . $scheduleName . ')';
    }
    public function getValueAttribute(){
        return $this->id;
    }

    
}
