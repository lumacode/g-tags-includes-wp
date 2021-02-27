jQuery( ($) => {
    
    const form = document.getElementById('formTags')
    const url = '?page=gtags_menu';

    form.addEventListener('submit', (e) => {
        e.preventDefault()
        save();

        
    })

    function save() {
        //console.log('ejecutado');
        const data = $("#formTags").serialize(); // toma los datos 
            
            $.ajax({
                type: "post",
                url: url,
                data: data
            })
            .done(function(res){
                $('.alert-success-gtag').fadeTo("fast", 1);
            });
                
        }

        
   



});