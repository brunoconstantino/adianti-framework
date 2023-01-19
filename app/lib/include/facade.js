function __adianti_goto_page(page)
{
   window.location = page+'&encoding=utf8&jquery=no&isajax=1';
}

function __adianti_load_html(content)
{
   if ($('[widget="TWindow"]').length > 0)
   {
       $('[widget="TWindow"]').attr('remove', 'yes');
       // usar essa chamada aqui na facade do framework, por que daí não tem o #adianti_online_content
       //$('[widget="TWindow"]').after('<div></div>').html(content);
       $('#adianti_online_content').html(content);
       $('[widget="TWindow"][remove="yes"]').remove();
   }
   else
   {
       $('#adianti_div_content').html(content);
   }
}

function __adianti_load_page_no_register(page)
{
   if ($('[widget="TWindow"]').length > 0)
   {
        $.get(page, function(data)
        {
            $('[widget="TWindow"]').attr('remove', 'yes');
            // usar essa chamada aqui na facade do framework, por que daí não tem o #adianti_online_content
            //$('[widget="TWindow"]').after('<div></div>').html(data);
            $('#adianti_online_content').html(data);
            $('[widget="TWindow"][remove="yes"]').remove();
        });
   }
   else
   {
       $('#adianti_div_content').load(page);
   }
}

function __adianti_append_page(page)
{
    $.get(page+'&encoding=utf8&jquery=no&isajax=1', function(data)
    {
        $('#adianti_online_content').after('<div></div>').html(data);
    });
}

function __adianti_load_page(page)
{
    url = page+'&encoding=utf8&jquery=no&isajax=1';
    url = url.replace('index.php', 'engine.php');
    __adianti_load_page_no_register(url);
    
    if ( history.pushState && ($('[widget="TWindow"]').length == 0) )
    {
        var stateObj = { url: url };
        history.pushState(stateObj, "", url.replace('engine.php', 'index.php'));
    }
}