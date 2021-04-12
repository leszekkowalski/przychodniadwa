


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
let kalendarz=new Date(NaN);
//nowy obiekt daty dla nowego Wydarzenie
let wydarzenie=new Date(NaN);

// Wyodrębnij miesiąc i rok z identyfikatora $miesiac_ID elementu h3
let idWydarzenie=$("h3").attr("id").split('-');

let date=entry["wydarzenie_fieldset[wydarzenie_data]"].split(' ')[0],
data_podzielona=date.split('-');

// Ustaw datę w obiekcie kalendarza
kalendarz.setUTCFullYear(idWydarzenie[1],idWydarzenie[2],1);

// Ustaw datę w obiekcie wydarzenia
wydarzenie.setUTCFullYear(data_podzielona[0],data_podzielona[1],data_podzielona[2]);
// Ponieważ obiekt daty jest tworzony na podstawie
// czasu uniwersalnego, a następnie dostosowywany do strefy czasowej użytkownika,
// dodaj lub odejmij określoną liczbę minut, aby uzyskać właściwą datę
wydarzenie.setMinutes(wydarzenie.getTimezoneOffset());
console.log(idWydarzenie);
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
console.log(key);
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

