<h2 style="color: red">{{ $exception->getMessage() }}</h2>
<a href="#" onclick="goBack()" class="btn btn-primary button-app">Go Back</a>

<script type="application/javascript">
    function goBack() {
        window.history.back();
    }
</script>
