<script>
    var modalLoading = new Modal_loading();

    var modalConfirmDeleteAllNotifications = new ModalConfirm('modalConfirm_delete_all_notifications');
    modalConfirmDeleteAllNotifications.setHeader("@lang('confirm.Delete.AllNotifications.Header')");
    modalConfirmDeleteAllNotifications.setBody("@lang('confirm.Delete.AllNotifications.Body')");

    modalConfirmDeleteAllNotifications.setOnYesPressed(function() {
        try {
            return document.getElementById('DeleteAllNotifications').click();
        } catch (error) {
            alert(error.message)
        }
    });
</script>
