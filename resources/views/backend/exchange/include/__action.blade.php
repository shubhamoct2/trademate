@can('internal-transfer-manage')
    <span type="button"
          data-tnx="{{$tnx}}"      
          data-name="{{$username}}"    
          data-type="{{$description}}"   
          data-amount="{{$final_amount}}" 
          class="review-transaction"
    ><button class="round-icon-btn red-btn" data-bs-toggle="tooltip" title="Review"
             data-bs-original-title="Send Email"><i icon-name="edit-3"></i></button></span>
@endcan

<script>
    lucide.createIcons();
    $(document).ajaxComplete(function () {
        "use strict";
        $('[data-bs-toggle="tooltip"]').tooltip({
            "html": true,
        });
    });
</script>
