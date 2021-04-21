


let input = $( "form input:checkbox" )
  //.wrap( "<span></span>" )
  .parent()
  .css({
    background: "white",
   // border: "1px red solid"
  });
  
  
var fx = {
    
// Dodaje nowe wydarzenie po jego zapisaniu do kodu HTML
"dodajWydarzenieDoKoduHtml" : function(data, formData){
// Przekształć łańcuch zapytania w obiekt
let entry = fx.deserialize(formData);

//nowy obiekt daty dla biezacego miesjaca
let cal=new Date(NaN);
//nowy obiekt daty dla nowego Wydarzenie
let event=new Date(NaN);

// Wyodrębnij dzień, miesiąc i rok wydarzenia
let date=entry["wydarzenie_fieldset[wydarzenie_data]"].split(' ')[0];

// Podziel datę wydarzenia
edata = date.split('-');
        
// Wyodrębnij miesiąc i rok z identyfikatora $miesiac_ID elementu h3
let cdata=$("h3").attr("id").split('-');



// Ustaw datę w obiekcie kalendarza
cal.setUTCFullYear(cdata[1],cdata[2]-1,1);

// Ustaw datę w obiekcie wydarzenia
event.setUTCFullYear(edata[0],edata[1]-1,edata[2]);
// Ponieważ obiekt daty jest tworzony na podstawie
// czasu uniwersalnego, a następnie dostosowywany do strefy czasowej użytkownika,
// dodaj lub odejmij określoną liczbę minut, aby uzyskać właściwą datę
event.setMinutes(event.getTimezoneOffset());

// Jeżeli rok i miesiąc się zgadzają, rozpocznij proces
// dodawania nowego wydarzenia do kalendarza
if ( cal.getFullYear()==event.getFullYear() && cal.getMonth()==event.getMonth() )
{
// Pobierz dzień miesiąca wydarzenia
let day = String(event.getDate());
// Poprzedź jednocyfrowe dni zerem
day = day.length==1 ? "0"+day : day;

let idwydarzenie = parseInt(data);

if(idwydarzenie>0){
let href_1='../kalendarz/pokaz-wydarzenie/'+idwydarzenie+'/';
let href=href_1+entry["wydarzenie_fieldset[wydarzenie_idlekarz]"]+'&id='+idwydarzenie+'&idlekarz='+entry["wydarzenie_fieldset[wydarzenie_idlekarz]"]+"&data="+entry["wydarzenie_fieldset[wydarzenie_data]"];

// Dodaj nowy odnośnik
$("<a>")
.hide()
.attr("z-index",1)
.attr("href", href)
.text(entry["wydarzenie_fieldset[wydarzenie_tytul]"]).addClass("link")
.insertAfter($("strong:contains("+day+")"))
.delay(1000)
.fadeIn("slow");
}

}

//console.log(cal);
//console.log(event);
},
"dodajWydarzenieDoKoduHtmldzien":function(data, formData,idlekarz){
 // Przekształć łańcuch zapytania w obiekt
let DaneFormularza = fx.deserialize(formData);  

//nowy obiekt daty dla biezacego dnia
let dzien=new Date(NaN);
//nowy obiekt daty dla nowego Wydarzenie
let noweWydarzenie=new Date(NaN);

// Wyodrębnij dzień, miesiąc i rok wydarzenia
let DzienMiesiacRokWydarzenia=DaneFormularza["wydarzenie_fieldset[wydarzenie_data]"].split(' ')[0];

// Podziel datę wydarzenia
let DzienMiesiacRokWydarzeniaPodzielony = DzienMiesiacRokWydarzenia.split('-');

let DzienMiesiacRokWydarzeniaKodHtml=$("#kalendarz").attr("data_kodhtml");
let DzienMiesiacRokWydarzeniaKodHtmlPodzielony=DzienMiesiacRokWydarzeniaKodHtml.split("-");
// Ustaw datę w obiekcie kalendarza
dzien.setUTCFullYear(DzienMiesiacRokWydarzeniaKodHtmlPodzielony[0],DzienMiesiacRokWydarzeniaKodHtmlPodzielony[1]-1,DzienMiesiacRokWydarzeniaKodHtmlPodzielony[2]);

noweWydarzenie.setUTCFullYear(DzienMiesiacRokWydarzeniaPodzielony[0],DzienMiesiacRokWydarzeniaPodzielony[1]-1,DzienMiesiacRokWydarzeniaPodzielony[2]);

// Ponieważ obiekt daty jest tworzony na podstawie
// czasu uniwersalnego, a następnie dostosowywany do strefy czasowej użytkownika,
// dodaj lub odejmij określoną liczbę minut, aby uzyskać właściwą datę
noweWydarzenie.setMinutes(noweWydarzenie.getTimezoneOffset());

if ( dzien.getFullYear()===noweWydarzenie.getFullYear() && dzien.getMonth()===noweWydarzenie.getMonth() && dzien.getDay()===noweWydarzenie.getDay() ){
 
let dataRozpoczecia=DaneFormularza["wydarzenie_fieldset[wydarzenie_start]"].split(':'); 
let dataRozpoczeciaGodzina=dataRozpoczecia[0];
let dataRozpoczeciaMinuta=dataRozpoczecia[1]; 
let dataZakonczenia=DaneFormularza["wydarzenie_fieldset[wydarzenie_koniec]"].split(':'); 
let dataZakonczeniaGodzina=dataZakonczenia[0];
let dataZakonczeniaMinuta=dataZakonczenia[1]; 

let clasa='#wiersz-'+dataRozpoczeciaGodzina;
let szerokosc=500;
let minuty_jako_marginTop= dataRozpoczeciaMinuta;
szerokosc=szerokosc+minuty_jako_marginTop;

let godz1=new Date(2021,1,1,dataRozpoczeciaGodzina,dataRozpoczeciaMinuta);
let godz2=new Date(2021,1,1,dataZakonczeniaGodzina,dataZakonczeniaMinuta);
let wynik_wysokosc_Minuty=(godz2.getTime() - godz1.getTime())/60000;

let idwydarzenie = parseInt(data);

let wnetrze_a=DaneFormularza["wydarzenie_fieldset[wydarzenie_tytul]"]+'  '+DaneFormularza["wydarzenie_fieldset[wydarzenie_start]"]+' - '+DaneFormularza["wydarzenie_fieldset[wydarzenie_koniec]"];

let href='../kalendarz/pokaz-wydarzenie/'+idwydarzenie+'/'+idlekarz+'?id='+idwydarzenie+'&idlekarz='+idlekarz;
let a='<a href='+href+
        ' style="margin-top:'+minuty_jako_marginTop+'px;height:'+wynik_wysokosc_Minuty+'px; background-color:red; z-index:15; "'+' id="a"'+' class="link" >'+wnetrze_a+'</a>';

if(idwydarzenie>0)
{
 // Dodaj nowy odnośnik
$(a).hide().insertAfter(clasa).delay(1000).fadeIn("slow");

}

}


},
/////////edytuje wyadzrenie (usuwa stare i wstawia nowe
"edytujWydarzenieDoKoduHtmldzien":function(data, formData,idwydarzenieWpisane){
 // Przekształć łańcuch zapytania w obiekt
let DaneFormularza = fx.deserialize(formData);  

//nowy obiekt daty dla biezacego dnia
let dzien=new Date(NaN);
//nowy obiekt daty dla nowego Wydarzenie
let noweWydarzenie=new Date(NaN);

// Wyodrębnij dzień, miesiąc i rok wydarzenia
let DzienMiesiacRokWydarzenia=DaneFormularza["wydarzenie_fieldset[wydarzenie_data]"].split(' ')[0];

// Podziel datę wydarzenia
let DzienMiesiacRokWydarzeniaPodzielony = DzienMiesiacRokWydarzenia.split('-');

let DzienMiesiacRokWydarzeniaKodHtml=$("#kalendarz").attr("data_kodhtml");
let DzienMiesiacRokWydarzeniaKodHtmlPodzielony=DzienMiesiacRokWydarzeniaKodHtml.split("-");
// Ustaw datę w obiekcie kalendarza
dzien.setUTCFullYear(DzienMiesiacRokWydarzeniaKodHtmlPodzielony[0],DzienMiesiacRokWydarzeniaKodHtmlPodzielony[1]-1,DzienMiesiacRokWydarzeniaKodHtmlPodzielony[2]);

noweWydarzenie.setUTCFullYear(DzienMiesiacRokWydarzeniaPodzielony[0],DzienMiesiacRokWydarzeniaPodzielony[1]-1,DzienMiesiacRokWydarzeniaPodzielony[2]);

// Ponieważ obiekt daty jest tworzony na podstawie
// czasu uniwersalnego, a następnie dostosowywany do strefy czasowej użytkownika,
// dodaj lub odejmij określoną liczbę minut, aby uzyskać właściwą datę
noweWydarzenie.setMinutes(noweWydarzenie.getTimezoneOffset());

if ( dzien.getFullYear()===noweWydarzenie.getFullYear() && dzien.getMonth()===noweWydarzenie.getMonth() && dzien.getDay()===noweWydarzenie.getDay() ){
 
let dataRozpoczecia=DaneFormularza["wydarzenie_fieldset[wydarzenie_start]"].split(':'); 
let dataRozpoczeciaGodzina=dataRozpoczecia[0];
let dataRozpoczeciaMinuta=dataRozpoczecia[1]; 
let dataZakonczenia=DaneFormularza["wydarzenie_fieldset[wydarzenie_koniec]"].split(':'); 
let dataZakonczeniaGodzina=dataZakonczenia[0];
let dataZakonczeniaMinuta=dataZakonczenia[1]; 

let clasa='#wiersz-'+dataRozpoczeciaGodzina;
let szerokosc=500;
let minuty_jako_marginTop= dataRozpoczeciaMinuta;
szerokosc=szerokosc+minuty_jako_marginTop;

let godz1=new Date(2021,1,1,dataRozpoczeciaGodzina,dataRozpoczeciaMinuta);
let godz2=new Date(2021,1,1,dataZakonczeniaGodzina,dataZakonczeniaMinuta);
let wynik_wysokosc_Minuty=(godz2.getTime() - godz1.getTime())/60000;

let idwydarzenie = parseInt(data);
let idlekarz=DaneFormularza["wydarzenie_fieldset[wydarzenie_idlekarz]"];
let wnetrze_a=DaneFormularza["wydarzenie_fieldset[wydarzenie_tytul]"]+'  '+DaneFormularza["wydarzenie_fieldset[wydarzenie_start]"]+' - '+DaneFormularza["wydarzenie_fieldset[wydarzenie_koniec]"];

let href='../kalendarz/pokaz-wydarzenie/'+idwydarzenie+'/'+idlekarz+'?id='+idwydarzenie+'&idlekarz='+idlekarz;
let a='<a href='+href+
        ' style="margin-top:'+minuty_jako_marginTop+'px;height:'+wynik_wysokosc_Minuty+'px; background-color:red; z-index:15; "'+' id="a"'+' class="link" >'+wnetrze_a+'</a>';

if(idwydarzenie>0)
{
  // Usuń każde wydarzenie z klasą "active"
        $(".active").fadeOut("slow", function(){
            $(this).remove();
                        });         
 // Dodaj nowy odnośnik
$(a).hide().insertAfter(clasa).delay(1000).fadeIn("slow");

}

}else{
    // Usuń każde wydarzenie z klasą "active"
        $(".active").fadeOut("slow", function(){
            $(this).remove();
                        });            
}


},
// Przeprowadza deserializację łańcucha zapytania i zwraca
// obiekt wydarzenia
"deserialize" : function(str){
// Rozbij parę nazwa-wartość
let data = str.split("&"),
// Deklaruj zmienne potrzebne w pętli
pairs=[], entry={}, key, val; 

// Przekształć każdą parę nazwa-wartość we właściwość obiektu
for (let x in data )
{
// Rozdziel każdą parę i zapisz otrzymane elementy w tablicy
pairs = data[x].split("=");
// Pierwszy element to nazwa parametru
key = decodeURIComponent(pairs[0]);
//key=pairs[0].replace(/%5B/, '_');
//key2=key.replace(/%5D/, '_');
// Drugi element to wartość parametru
val = pairs[1];

// Przywróć łańcuchowi zakodowanemu w URL oryginalną postać
// i zapisz każdą wartość jako właściwość obiektu
entry[key] = fx.url_decode(val);
}

return entry;
},
// Dekoduje łańcuch zapytania
"url_decode" : function(str) {
// Zamienia znaki plus na spacje
let znaki_plus_na_spacje = str.replace(/\+/g, ' ');
// Zamienia encje na odpowiadające im znaki
return decodeURIComponent(znaki_plus_na_spacje);

},

// Sprawdza poprawność łańcucha daty (RRRR-MM-DD GG:MM:SS)
"kontrola_datyPK": function(date)
{
// Definiuj wyrażenie regularne, które będzie wzorcem poprawnego formatu np. 10:22
let pattern = /^ (\d{2})(:\d{2})$/;
// Zwróć true przy dopasowaniu albo false w przeciwnym wypadku
return date.match(pattern)!==null;
},
"kontrola_daty": function(date)
{
// Definiuj wyrażenie regularne, które będzie wzorcem poprawnego formatu np. 202`-12-12
let pattern =/^ (\d{4}(-\d{2}){2})$/;
// Zwróć true przy dopasowaniu albo false w przeciwnym wypadku
return date.match(pattern)!==null;
},

};

