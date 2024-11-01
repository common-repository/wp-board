

function getData()
{
    if (xmlhttp.readyState==4 || xmlhttp.readyState=="complete")
    {
    document.getElementById("actiondiv").className='hiddenpopup';
      document.getElementById("boardcontent").innerHTML=xmlhttp.responseText;
    }
}

function reffun()
{
    if (xmlhttp.readyState==4 || xmlhttp.readyState=="complete")
    {
    document.getElementById("actiondiv").className='hiddenpopup';
     refresh();
    }
}

function GetXmlHttpObject()
{
         if (window.XMLHttpRequest)
         {
          // code for IE7+, Firefox, Chrome, Opera, Safari
                   return new XMLHttpRequest();
         }
         if (window.ActiveXObject)
         {
          // code for IE6, IE5
                   return new ActiveXObject("Microsoft.XMLHTTP");
         }
         return null;
}
