<style>
    .d-card {
        background-image: linear-gradient(to right, #00A3FF, #023D5F);
        width: 265px;
        height: 110px;
        top: 122px;
        left: 287px;
        border-radius: 20px;
    }

    .card-text {
        padding: 20px;
        color: white;
    }
</style>
<div class="d-card">
    <div class="d-flex">
        <div class="card-text">
            <h4>{{ $value }}</h4>
            <h5>{{ $label }}</h5>
        </div>
        <div class="my-auto mx-auto">
            <img src="{{ $svg }}" alt="SVG Image">
        </div>
    </div>
</div>