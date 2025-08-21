<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestingYourIdeaResource extends JsonResource
{
    public function toArray($request)
    {
        if (is_null($this->resource)) {
            return [];
        }
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'business_id' => $this->business_id,
            'your_idea' => $this->your_idea,
            'desirability' => [
                'solves_problem' => $this->solves_problem,
                'problem_statement' => $this->problem_statement,
                'existing_solutions_used' => $this->existing_solutions_used,
                'current_solutions_details' => $this->current_solutions_details,
                'switch_reason' => $this->switch_reason,
                'notes' => $this->desirability_notes,
            ],
            
            'feasibility' => [
                'required_skills' => $this->required_skills,
                'qualifications_permits' => $this->qualifications_permits,
                'notes' => $this->feasibility_notes,
            ],
            
            'viability' => [
                'payment_possible' => $this->payment_possible,
                'profitability' => $this->profitability,
                'finances_details' => $this->finances_details,
                'notes' => $this->viability_notes,
            ],
            
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}