<style>
    .btn-bg{
        background-image: linear-gradient( #00A3FF, #023D5F);
        border: none;
        border-radius: 6px;
    }
    .btn-bg:active{
        background-image: linear-gradient( #023D5F, #00A3FF) !important;
    }
</style>
<button class="btn btn-md btn-bg text-white" data-toggle="modal" data-target="{{ $dataTarget }}">
    {{$value}}
</button>