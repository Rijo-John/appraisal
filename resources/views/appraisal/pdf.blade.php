<!DOCTYPE html>
<html>
<head>
    <title>Appraisal PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.5; }
        .heading-color { font-weight: bold; color: #333; }
        .section { margin-bottom: 20px; }
        .goal { margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .goal-rating { margin-top: 10px; }
        .goal-number { font-weight: bold; }
        .evidence-link { font-style: italic; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
    </style>
</head>
<body>

    <h2>My Goals</h2>

    @foreach($user_goals as $index => $goal)
        <div class="section goal">
            <div class="goal-number">Goal {{ $index + 1 }}</div>
            <div><strong>Description:</strong> {{ $goal->goal }}</div>
            <div><strong>Weightage:</strong> {{ $goal->weightage }}</div>

            @if(!empty($goalWiseData[$goal->id]))
                @foreach($goalWiseData[$goal->id] as $dataIndex => $data)
                    <div class="goal-rating">
                        <div><span class="label">Rating:</span>
                            @switch($data->rating)
                                @case(10) Achieved @break
                                @case(5) Partially Achieved @break
                                @case(1) Not Achieved @break
                                @case(0) Not Applicable @break
                                @default N/A
                            @endswitch
                        </div>

                        <div><span class="label">Task Details:</span> {{ $data->employee_comment }}</div>

                        @if(!empty($data->attachment))
                            <div><span class="label">Evidence:</span> 
                                <span class="evidence-link">
                                    {{ basename($data->attachment) }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="goal-rating">
                    <div><span class="label">Rating:</span> Not Provided</div>
                    <div><span class="label">Task Details:</span> -</div>
                    <div><span class="label">Evidence:</span> -</div>
                </div>
            @endif
        </div>
    @endforeach

    <h3>General Comments</h3>
    <div class="section">
        <div><span class="label">Key Contributions:</span> {{ $submittedGeneralData?->key_contributions ?? '-' }}</div>
        <div><span class="label">Appraiser Comments:</span> {{ $submittedGeneralData?->suggestions_for_improvement ?? '-' }}</div>
    </div>

</body>
</html>
