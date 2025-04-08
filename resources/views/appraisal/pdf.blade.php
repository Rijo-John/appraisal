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

    @foreach ($user_goals as $index => $goal)
        <div class="section">
            <strong>Goal {{ $index + 1 }}</strong><br>
            <div><span class="label">Description:</span> {{ $goal->goal ?? '-' }}</div>
            <div><span class="label">Weightage:</span> {{ $goal->weightage ?? '-' }}</div>
            <div><span class="label">Rating:</span> {{ $ratingLabels[(int)$goal->rating] ?? 'Not Provided' }}</div>

            <div><span class="label">Employee Comment:</span> {{ $goal->employee_comment ?? '-' }}</div>
            <div><span class="label">Attachment:</span> {{ $goal->attachment ?? '-' }}</div>
        </div>
    @endforeach


    <h3>General Comments</h3>
    <div class="section">
        <div><span class="label">Key Contributions:</span> {{ $submittedGeneralData?->key_contributions ?? '-' }}</div>
        <div><span class="label">Appraiser Comments:</span> {{ $submittedGeneralData?->suggestions_for_improvement ?? '-' }}</div>
    </div>

</body>
</html>
