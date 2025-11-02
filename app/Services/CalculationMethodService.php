<?php
namespace App\Services;

use App\Models\CalculationMethod;
use App\Models\DrivingActivity;
use App\Models\Milestone;
use App\Models\Window;
use Carbon\Carbon;

class CalculationMethodService
{
    public function comp_date($project_id, $window_id, $prog, $ms = [])
    {
        $query = DrivingActivity::where('project_id', $project_id)
            ->where('window_id', $window_id)
            ->where('program', $prog);

        if (! empty($ms)) {
            $query->whereIn('milestone_id', $ms);
        }

        // Get the latest (newest) ms_come_date
        $latestDate = $query->max('ms_come_date');

        return $latestDate;
    }

    public function activities_num($project_id, $window_id, $prog, $ms, $liability)
    {
        $date             = $this->comp_date($project_id, $window_id, $prog, [$ms]);
        $activities_count = DrivingActivity::where('project_id', $project_id)
            ->where('window_id', $window_id)
            ->where('program', $prog)->where('milestone_id', $ms)->where('liability', $liability)->where('ms_come_date', $date)->count();
        return $activities_count;
    }

    public function compare_dates($date1, $date2)
    {
        $date1 = Carbon::parse($date1);
        $date2 = Carbon::parse($date2);

        if ($date1->gt($date2)) {
            return "after";
        } elseif ($date1->lt($date2)) {
            return "before";
        } else {
            return "equal";
        }
    }

    public function last_ms($project_id, $window_id)
    {
        $milestones_IDs = Milestone::where('project_id', auth()->user()->current_project_id)->pluck('id')->toArray();
        $date           = $this->comp_date($project_id, $window_id, 'UPD', $milestones_IDs);
        $row            = DrivingActivity::where('project_id', $project_id)
            ->where('window_id', $window_id)
            ->where('program', 'UPD')->where('ms_come_date', $date)->orderByDesc('id')
            ->first();
        $milestone = $row->milestone_id;
        return $milestone;
    }

    public function fnExcusableOFLastMS($project_id, $window_id)
    {
        $milestone = $this->last_ms($project_id, $window_id);
        $result    = $this->excusable($project_id, $window_id, $milestone);
        return $result;
    }

    public function culpable($project_id, $window_id, $ms)
    {
        $result           = 0;
        $activities_count = $this->activities_num($project_id, $window_id, 'UPD', $ms, 'Culpable');
        if ($activities_count > 0) {
            $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
            $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
            if ($this->compare_dates($date1, $date2) === 'after') {
                $start    = Carbon::parse($date2);
                $end      = Carbon::parse($date1);
                $duration = $start->diffInDays($end, false);
                $result   = $duration;
            } elseif ($this->compare_dates($date1, $date2) === 'equal') {
                $InCaseOfConcurrency = CalculationMethod::where('project_id', $project_id)->where('key', 'InCaseOfConcurrency')->first();
                $InCaseOfConcurrency = $InCaseOfConcurrency ? $InCaseOfConcurrency->value : '1';
                if ($InCaseOfConcurrency == '1') {
                    $result = 0;
                } else {
                    $date3    = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date3);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                }
            } else {
                $date3    = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
                $start    = Carbon::parse($date2);
                $end      = Carbon::parse($date3);
                $duration = $start->diffInDays($end, false);
                $result   = $duration;
            }
        } elseif ($activities_count == 0) {
            $date1 = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
            $date2 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
            if ($this->compare_dates($date1, $date2) === 'after') {
                $WhatIfUPDExtendedAsExcusableTookLonger = CalculationMethod::where('project_id', $project_id)->where('key', 'WhatIfUPDExtendedAsExcusableTookLonger')->first();
                $WhatIfUPDExtendedAsExcusableTookLonger = $WhatIfUPDExtendedAsExcusableTookLonger ? $WhatIfUPDExtendedAsExcusableTookLonger->value : '2';
                if ($WhatIfUPDExtendedAsExcusableTookLonger == '1') {
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date1);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } else {
                    $result = 0;
                }
            } elseif ($this->compare_dates($date1, $date2) === 'before' || $this->compare_dates($date1, $date2) === 'equal') {
                $HowToDealWithMitigation = CalculationMethod::where('project_id', $project_id)->where('key', 'HowToDealWithMitigation')->first();
                $HowToDealWithMitigation = $HowToDealWithMitigation ? $HowToDealWithMitigation->value : '2';
                if ($HowToDealWithMitigation == '1') {
                    // $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date1);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } elseif ($HowToDealWithMitigation == '2') {

                    $result = 0;
                }
            }
        }
        return $result;
    }
    public function excusable($project_id, $window_id, $ms)
    {
        $result                         = 0;
        $UPD_Excusable_activities_count = $this->activities_num($project_id, $window_id, 'UPD', $ms, 'Excusable');
        $UPD_Culpable_activities_count  = $this->activities_num($project_id, $window_id, 'UPD', $ms, 'Culpable');
        if ($UPD_Excusable_activities_count > 0 && $UPD_Culpable_activities_count == 0) {
            $date1 = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
            $date2 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
            if ($this->compare_dates($date1, $date2) === 'equal') {
                $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                $start    = Carbon::parse($date3);
                $end      = Carbon::parse($date2);
                $duration = $start->diffInDays($end, false);
                return $duration;
            } elseif ($this->compare_dates($date1, $date2) === 'before') {
                $HowToDealWithMitigation = CalculationMethod::where('project_id', $project_id)->where('key', 'HowToDealWithMitigation')->first();
                $HowToDealWithMitigation = $HowToDealWithMitigation ? $HowToDealWithMitigation->value : '2';
                if ($HowToDealWithMitigation == '1') {
                    $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    $start    = Carbon::parse($date3);
                    $end      = Carbon::parse($date2);
                    $duration = $start->diffInDays($end, false);
                    return $duration;
                } elseif ($HowToDealWithMitigation == '2') {
                    $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    $start    = Carbon::parse($date3);
                    $end      = Carbon::parse($date1);
                    $duration = $start->diffInDays($end, false);
                    return $duration;
                }
            } elseif ($this->compare_dates($date1, $date2) === 'after') {
                $WhatIfUPDExtendedAsExcusableTookLonger = CalculationMethod::where('project_id', $project_id)->where('key', 'WhatIfUPDExtendedAsExcusableTookLonger')->first();
                $WhatIfUPDExtendedAsExcusableTookLonger = $WhatIfUPDExtendedAsExcusableTookLonger ? $WhatIfUPDExtendedAsExcusableTookLonger->value : '2';
                if ($WhatIfUPDExtendedAsExcusableTookLonger == '1') {
                    $date3 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date2, $date3) === 'after') {
                        $start    = Carbon::parse($date3);
                        $end      = Carbon::parse($date2);
                        $duration = $start->diffInDays($end, false);
                        return $duration;
                    } else {
                        return 0;
                    }
                } elseif ($WhatIfUPDExtendedAsExcusableTookLonger == '2') {
                    $date3 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date2, $date3) === 'after') {
                        $start    = Carbon::parse($date3);
                        $end      = Carbon::parse($date1);
                        $duration = $start->diffInDays($end, false);
                        return $duration;
                    } else {
                        return 0;
                    }
                }
            }
        } elseif ($UPD_Excusable_activities_count > 0 && $UPD_Culpable_activities_count > 0) {
            $InCaseOfConcurrency = CalculationMethod::where('project_id', $project_id)->where('key', 'InCaseOfConcurrency')->first();
            $InCaseOfConcurrency = $InCaseOfConcurrency ? $InCaseOfConcurrency->value : '2';
            if ($InCaseOfConcurrency == '2') {
                return 0;
            }
            $date1 = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
            $date2 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
            if ($this->compare_dates($date1, $date2) === 'equal') {
                $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                $start    = Carbon::parse($date3);
                $end      = Carbon::parse($date2);
                $duration = $start->diffInDays($end, false);
                return $duration;
            } elseif ($this->compare_dates($date1, $date2) === 'before') {
                $HowToDealWithMitigation = CalculationMethod::where('project_id', $project_id)->where('key', 'HowToDealWithMitigation')->first();
                $HowToDealWithMitigation = $HowToDealWithMitigation ? $HowToDealWithMitigation->value : '2';
                if ($HowToDealWithMitigation == '1') {
                    $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    $start    = Carbon::parse($date3);
                    $end      = Carbon::parse($date2);
                    $duration = $start->diffInDays($end, false);
                    return $duration;
                } elseif ($HowToDealWithMitigation == '2') {
                    $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    $start    = Carbon::parse($date3);
                    $end      = Carbon::parse($date1);
                    $duration = $start->diffInDays($end, false);
                    return $duration;
                }
            } elseif ($this->compare_dates($date1, $date2) === 'after') {
                $WhatIfUPDExtendedAsExcusableTookLonger = CalculationMethod::where('project_id', $project_id)->where('key', 'WhatIfUPDExtendedAsExcusableTookLonger')->first();
                $WhatIfUPDExtendedAsExcusableTookLonger = $WhatIfUPDExtendedAsExcusableTookLonger ? $WhatIfUPDExtendedAsExcusableTookLonger->value : '2';
                if ($WhatIfUPDExtendedAsExcusableTookLonger == '1') {
                    $date3 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date2, $date3) === 'after') {
                        $start    = Carbon::parse($date3);
                        $end      = Carbon::parse($date2);
                        $duration = $start->diffInDays($end, false);
                        return $duration;
                    } else {
                        return 0;
                    }
                } elseif ($WhatIfUPDExtendedAsExcusableTookLonger == '2') {
                    $date3 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date2, $date3) === 'after') {
                        $start    = Carbon::parse($date3);
                        $end      = Carbon::parse($date1);
                        $duration = $start->diffInDays($end, false);
                        return $duration;
                    } else {
                        return 0;
                    }
                }
            }
        } elseif ($UPD_Excusable_activities_count == 0 && $UPD_Culpable_activities_count > 0) {
            $InCaseOfConcurrency = CalculationMethod::where('project_id', $project_id)->where('key', 'InCaseOfConcurrency')->first();
            $InCaseOfConcurrency = $InCaseOfConcurrency ? $InCaseOfConcurrency->value : '2';
            if ($InCaseOfConcurrency == '2') {
                return 0;
            }
            $date1 = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
            $date2 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
            if ($this->compare_dates($date1, $date2) === 'equal') {
                $date3    = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                $start    = Carbon::parse($date3);
                $end      = Carbon::parse($date2);
                $duration = $start->diffInDays($end, false);
                return $duration;
            } elseif ($this->compare_dates($date1, $date2) === 'before') {
                $HowToDealWithMitigation = CalculationMethod::where('project_id', $project_id)->where('key', 'HowToDealWithMitigation')->first();
                $HowToDealWithMitigation = $HowToDealWithMitigation ? $HowToDealWithMitigation->value : '2';
                if ($HowToDealWithMitigation == '1') {
                    $date3 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date2, $date2) === 'after') {
                        $start    = Carbon::parse($date3);
                        $end      = Carbon::parse($date2);
                        $duration = $start->diffInDays($end, false);
                        return $duration;
                    } else {
                        return 0;
                    }
                } elseif ($HowToDealWithMitigation == '2') {
                    $date3 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date2, $date2) === 'after') {
                        $start    = Carbon::parse($date3);
                        $end      = Carbon::parse($date1);
                        $duration = $start->diffInDays($end, false);
                        return $duration;
                    } else {
                        return 0;
                    }
                }
            }
        }
    }
    public function excusable2($project_id, $window_id, $ms)
    {
        $result                         = 0;
        $UPD_Excusable_activities_count = $this->activities_num($project_id, $window_id, 'UPD', $ms, 'Excusable');
        $UPD_Culpable_activities_count  = $this->activities_num($project_id, $window_id, 'UPD', $ms, 'Culpable');
        if ($UPD_Excusable_activities_count > 0 && $UPD_Culpable_activities_count == 0) {
            $WhatIfUPDExtendedAsExcusableTookLonger = CalculationMethod::where('project_id', $project_id)->where('key', 'WhatIfUPDExtendedAsExcusableTookLonger')->first();
            $WhatIfUPDExtendedAsExcusableTookLonger = $WhatIfUPDExtendedAsExcusableTookLonger ? $WhatIfUPDExtendedAsExcusableTookLonger->value : '2';
            if ($WhatIfUPDExtendedAsExcusableTookLonger == '1') {
                $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
                $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                if ($this->compare_dates($date1, $date2) === 'after') {
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date1);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } else {
                    $result = 0;
                }
            } else {
                $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
                $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                if ($this->compare_dates($date1, $date2) === 'after') {
                    $date3    = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date3);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } else {
                    $result = 0;
                }
            }
        } elseif ($UPD_Excusable_activities_count > 0 && $UPD_Culpable_activities_count > 0) {
            $WhatIfUPDExtendedAsExcusableTookLonger = CalculationMethod::where('project_id', $project_id)->where('key', 'WhatIfUPDExtendedAsExcusableTookLonger')->first();
            $WhatIfUPDExtendedAsExcusableTookLonger = $WhatIfUPDExtendedAsExcusableTookLonger ? $WhatIfUPDExtendedAsExcusableTookLonger->value : '2';
            if ($WhatIfUPDExtendedAsExcusableTookLonger == '1') {
                $HowToDealWithMitigation = CalculationMethod::where('project_id', $project_id)->where('key', 'HowToDealWithMitigation')->first();
                $HowToDealWithMitigation = $HowToDealWithMitigation ? $HowToDealWithMitigation->value : '2';
                if ($HowToDealWithMitigation == '1') {
                    $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
                    $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                    if ($this->compare_dates($date1, $date2) === 'after') {
                        $start    = Carbon::parse($date2);
                        $end      = Carbon::parse($date1);
                        $duration = $start->diffInDays($end, false);
                        $result   = $duration;
                    } else {
                        $result = 0;
                    }
                } elseif ($HowToDealWithMitigation == '2') {
                    $result = 0;
                }
            } elseif ($WhatIfUPDExtendedAsExcusableTookLonger == '2') {
                $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
                $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                if ($this->compare_dates($date1, $date2) === 'after') {
                    $date3    = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date3);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } else {
                    $result = 0;
                }
            }
        } elseif ($UPD_Excusable_activities_count == 0 && $UPD_Culpable_activities_count > 0) {
            $HowToDealWithMitigation = CalculationMethod::where('project_id', $project_id)->where('key', 'HowToDealWithMitigation')->first();
            $HowToDealWithMitigation = $HowToDealWithMitigation ? $HowToDealWithMitigation->value : '2';
            if ($HowToDealWithMitigation == '1') {
                $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
                $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                if ($this->compare_dates($date1, $date2) === 'after') {
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date1);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } else {
                    $result = 0;
                }
            } elseif ($HowToDealWithMitigation == '2') {
                $date1 = $this->comp_date($project_id, $window_id, 'IMP', [$ms]);
                $date2 = $this->comp_date($project_id, $window_id, 'BAS', [$ms]);
                if ($this->compare_dates($date1, $date2) === 'after') {
                    $date3    = $this->comp_date($project_id, $window_id, 'UPD', [$ms]);
                    $start    = Carbon::parse($date2);
                    $end      = Carbon::parse($date3);
                    $duration = $start->diffInDays($end, false);
                    $result   = $duration;
                } else {
                    $result = 0;
                }
            }
        }
        return $result;
    }

    public function compensable($project_id, $window_id)
    {
        $result                    = 0;
        $CompensabilityCalculation = CalculationMethod::where('project_id', $project_id)->where('key', 'CompensabilityCalculation')->first();
        $CompensabilityCalculation = $CompensabilityCalculation ? $CompensabilityCalculation->value : '2';
        if ($CompensabilityCalculation == '1') {
           
            $window          = Window::findOrFail($window_id);
            $previous_window = Window::where('project_id', auth()->user()->current_project_id)
                ->whereRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) < ?', [$window->no])
                ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) DESC')
                ->first();
            $fnExcusableOFLastMS = $this->fnExcusableOFLastMS($project_id, $window_id);
            if ($previous_window) {
                $compensableTransfer = $this->compensableTransfer($project_id, $previous_window->id);
            } else {
                $compensableTransfer = 0;
            }
            $result = $fnExcusableOFLastMS + $compensableTransfer;
            if ($result > $window->duration) {
                $result = $window->duration;
            }
        } elseif ($CompensabilityCalculation == '2') {
           
            $milestones_IDs      = Milestone::where('project_id', auth()->user()->current_project_id)->pluck('id')->toArray();
            $date1               = $this->comp_date($project_id, $window_id, 'UPD', $milestones_IDs);
            $date2               = $this->comp_date($project_id, $window_id, 'BUT', $milestones_IDs);
            $start               = Carbon::parse($date2);
            $end                 = Carbon::parse($date1);
            $duration            = $start->diffInDays($end, false);
            $result              = $duration;
            dd($result);
            $fnExcusableOFLastMS = $this->fnExcusableOFLastMS($project_id, $window_id);
            if ($result > $fnExcusableOFLastMS) {
                dd('dd');
                $result = $fnExcusableOFLastMS;
            }
            $window          = Window::findOrFail($window_id);
            $previous_window = Window::where('project_id', auth()->user()->current_project_id)
                ->whereRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) < ?', [$window->no])
                ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) DESC')
                ->first();
            if ($previous_window) {
                dd('ll');
                $compensableTransfer = $this->compensableTransfer($project_id, $previous_window->id);
            } else {
                $compensableTransfer = 0;
            }
            $result = $result + $compensableTransfer;
            if ($result > $window->duration) {
                $result = $window->duration;
            }
        }
        return $result;
    }

    public function compensableTransfer($project_id, $window_id)
    {
        $result                                  = 0;
        $WhatIfCompensableExceededWindowDuration = CalculationMethod::where('project_id', $project_id)->where('key', 'WhatIfCompensableExceededWindowDuration')->first();
        $WhatIfCompensableExceededWindowDuration = $WhatIfCompensableExceededWindowDuration ? $WhatIfCompensableExceededWindowDuration->value : '2';
        if ($WhatIfCompensableExceededWindowDuration == '2') {
            $CompensabilityCalculation = CalculationMethod::where('project_id', $project_id)->where('key', 'CompensabilityCalculation')->first();
            $CompensabilityCalculation = $CompensabilityCalculation ? $CompensabilityCalculation->value : '2';
            if ($CompensabilityCalculation == '1') {
                $window          = Window::findOrFail($window_id);
                $previous_window = Window::where('project_id', auth()->user()->current_project_id)
                    ->whereRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) < ?', [$window->no])
                    ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) DESC')
                    ->first();
                $fnExcusableOFLastMS = $this->fnExcusableOFLastMS($project_id, $window_id);
                if ($previous_window) {
                    $compensableTransfer = $this->compensableTransfer($project_id, $previous_window->id);
                } else {
                    $compensableTransfer = 0;
                }
                $x = $fnExcusableOFLastMS + $compensableTransfer;
                if ($x > $window->duration) {
                    $result = $x - $window->duration;
                } else {
                    $result = 0;
                }
            } elseif ($CompensabilityCalculation == '2') {
                $milestones_IDs      = Milestone::where('project_id', auth()->user()->current_project_id)->pluck('id')->toArray();
                $date1               = $this->comp_date($project_id, $window_id, 'UPD', $milestones_IDs);
                $date2               = $this->comp_date($project_id, $window_id, 'BUT', $milestones_IDs);
                $start               = Carbon::parse($date2);
                $end                 = Carbon::parse($date1);
                $duration            = $start->diffInDays($end, false);
                $x                   = $duration;
                $fnExcusableOFLastMS = $this->fnExcusableOFLastMS($project_id, $window_id);
                if ($x > $fnExcusableOFLastMS) {
                    $x = $fnExcusableOFLastMS;
                }
                $window          = Window::findOrFail($window_id);
                $previous_window = Window::where('project_id', auth()->user()->current_project_id)
                    ->whereRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) < ?', [$window->no])
                    ->orderByRaw('CAST(REGEXP_SUBSTR(no, "[0-9]+") AS UNSIGNED) DESC')
                    ->first();
                if ($previous_window) {
                    $compensableTransfer = $this->compensableTransfer($project_id, $previous_window->id);
                } else {
                    $compensableTransfer = 0;
                }
                $x = $x + $compensableTransfer;
                if ($x > $window->duration) {
                    $result = $x - $window->duration;
                } else {
                    $result = 0;
                }
            }
        }
        return $result;
    }

}
