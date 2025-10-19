<div>
    @if (count($DrivingActivities) > 0)
        <div id="drivingActivitiesContainer">
            @php
                $counter = 0;
            @endphp
            @foreach ($DrivingActivities as $DrivingActivity)
                <div class="driving-activity-row row" style="margin-left: 0px;">
                    <div class="col-md-4" style="padding-right: 0px;padding-left: 0px;">
                        <label>Milestones <span style="color:red">*</span></label>
                        <select class="form-control milestone-select" required
                            name="driving_activities[{{ $counter }}][milestone]">
                            <option value="" selected disabled>please select</option>
                            @foreach ($milestones as $milestone)
                                <option
                                    value="{{ $milestone->id }}"{{ $DrivingActivity->milestone_id == $milestone->id ? 'selected' : '' }}>
                                    {{ $milestone->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Activity <span style="color:red">*</span></label>
                        <select class="form-control activity-select" required
                            name="driving_activities[{{ $counter }}][activity]">
                            <option value="" selected disabled>please select</option>
                            @foreach ($activities as $activity)
                                <option
                                    value="{{ $activity->id }}"{{ $DrivingActivity->activity_id == $activity->id ? 'selected' : '' }}>
                                    {{ $activity->act_id . ' : ' . $activity->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3"style="padding-right: 0px;padding-left: 0px;">
                        <label>Completion Date <span style="color:red">*</span></label>
                        <input required type="date" style="background-color:#fff;"
                            name="driving_activities[{{ $counter }}][date]" class="form-control date"
                            placeholder="Completion Date" value="{{ $DrivingActivity->ms_come_date }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end"style="padding-left: 10px;">
                        <button type="button" class="btn btn-success btn-sm add-row mr-1"
                            style="height: calc(1.5em + 0.75rem + 5px);">+</button>
                        <button type="button"
                            class="btn btn-danger btn-sm remove-row"style="height: calc(1.5em + 0.75rem + 5px);">x</button>
                    </div>
                    <div class="col-md-6" style="padding-left: 0px;padding-right: 0px;margin-top:10px;">
                        <label>Liability <span style="color:red">*</span></label>
                        <select class="form-control" required
                            name="driving_activities[{{ $counter }}][liability]">
                            <option value="" selected disabled>please select</option>

                            <option
                                value="Excusable"{{ $DrivingActivity->liability == 'Excusable' ? 'selected' : '' }}>
                                Excusable
                            </option>
                            <option value="Culpable"{{ $DrivingActivity->liability == 'Culpable' ? 'selected' : '' }}>
                                Culpable
                            </option>
                            <option value="Neutral"{{ $DrivingActivity->liability == 'Neutral' ? 'selected' : '' }}>
                                Neutral
                            </option>
                            <option
                                value="Excusable took longer than estimated"{{ $DrivingActivity->liability === 'Excusable took longer than estimated' ? 'selected' : '' }}>
                                Excusable took longer than estimated
                            </option>

                        </select>
                    </div>
                    <div class="col-md-6" style="padding-right: 0px;margin-top:10px;">
                        <label>Claim File <span style="color:red">*</span></label>
                        <select class="form-control" required
                            name="driving_activities[{{ $counter }}][file]">
                            <option value="" selected disabled>please select</option>
                            @foreach ($claim_files as $file)
                                <option
                                    value="{{ $file->id }}"{{ $DrivingActivity->file_id == $file->id ? 'selected' : '' }}>
                                    {{ $file->code . ' : ' . $file->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @php
                    $counter++;
                @endphp
            @endforeach
        </div>
    @else
        <div id="drivingActivitiesContainer">
            <div class="driving-activity-row row" style="margin-left: 0px;">
                <div class="col-md-4" style="padding-right: 0px;padding-left: 0px;">
                    <label>Milestones <span style="color:red">*</span></label>
                    <select class="form-control milestone-select" required name="driving_activities[0][milestone]">
                        <option value="" selected disabled>please select</option>
                        @foreach ($milestones as $milestone)
                            <option value="{{ $milestone->id }}">{{ $milestone->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Activity <span style="color:red">*</span></label>
                    <select class="form-control activity-select" required name="driving_activities[0][activity]">
                        <option value="" selected disabled>please select</option>
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->act_id . ' : ' . $activity->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3"style="padding-right: 0px;padding-left: 0px;">
                    <label>Completion Date <span style="color:red">*</span></label>
                    <input required type="date" style="background-color:#fff;" name="driving_activities[0][date]"
                        class="form-control date" placeholder="Completion Date" value="">
                </div>

                <div class="col-md-1 d-flex align-items-end"style="padding-left: 10px;">
                    <button type="button" class="btn btn-success btn-sm add-row mr-1"
                        style="height: calc(1.5em + 0.75rem + 5px);">+</button>
                    <button type="button"
                        class="btn btn-danger btn-sm remove-row"style="height: calc(1.5em + 0.75rem + 5px);">x</button>
                </div>
                <div class="col-md-6" style="padding-left: 0px;padding-right: 0px;margin-top:10px;">
                    <label>Liability <span style="color:red">*</span></label>
                    <select class="form-control" required name="driving_activities[0][liability]">
                        <option value="" selected disabled>please select</option>

                        <option value="Excusable">
                            Excusable
                        </option>
                        <option value="Culpable">
                            Culpable
                        </option>
                        <option value="Neutral">
                            Neutral
                        </option>
                        <option value="Excusable took longer than estimated">
                            Excusable took longer than estimated
                        </option>

                    </select>
                </div>
                <div class="col-md-6" style="padding-right: 0px;margin-top:10px;">
                    <label>Claim File <span style="color:red">*</span></label>
                    <select class="form-control" required name="driving_activities[0][file]">
                        <option value="" selected disabled>please select</option>
                        @foreach ($claim_files as $file)
                            <option value="{{ $file->id }}">
                                {{ $file->code . ' : ' . $file->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

    @endif
    <div style="display: flex">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="customFile">Snip:</label>
                <div class="custom-file">
                    <input name="upd_snip" type="file" class="custom-file-input" id="customFile"
                        accept="image/*"onchange="previewImage(event,'imagePreview');updateFileName(this)">
                    <label class="custom-file-label" for="customFile"id="customFileLabel">Choose Image</label>
                </div>
                <!-- Image Preview -->
                <div class="mt-3">
                    <img id="imagePreview" src="{{ $upd_snip }}" alt="Image Preview" class="img-thumbnail"
                        style="@if ($upd_snip) display: block; @else display: none; @endif width: 200px; height: 120px;">
                </div>
            </div>
        </div>
       

    </div>


</div>
