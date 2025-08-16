(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var toggle = document.querySelector('.mobile-menu-toggle');
        var close = document.querySelector('.mobile-drawer-close');
        if (toggle) {
            toggle.addEventListener('click', function(e){
                e.preventDefault();
                document.body.classList.toggle('mobile-drawer-open');
            });
        }
        if (close) {
            close.addEventListener('click', function(e){
                e.preventDefault();
                document.body.classList.remove('mobile-drawer-open');
            });
        }
    });
})();
