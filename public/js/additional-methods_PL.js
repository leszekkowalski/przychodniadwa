

function kontroladlugosci11(value) {
    let dozwolonadlugosc=11;
    let dlugosc=value.length;
    if(dlugosc===dozwolonadlugosc){
        return true;
    }else{
        return false;
    }
}

function kontroladigit(value){
    let wynik;
    wynik=/[0-9]{11}/.test(value);
    return wynik;
}

function rozkodujPesel(pesel)
{
    //pobranie daty
    let rok     = parseInt(pesel.substring(0,2),10);
    let miesiac = parseInt(pesel.substring(2,4),10)-1;
    let dzien   = parseInt(pesel.substring(4,6),10);

    if(miesiac >= 80)
    {
        rok += 1800;
        miesiac = miesiac - 80;
    }
    else if(miesiac >= 60)
    {
        rok += 2200;
        miesiac = miesiac - 60;
    }
    else if (miesiac >= 40)
    {
        rok += 2100;
        miesiac = miesiac-40;
    }
    else if (miesiac >= 20)
    {
        rok += 2000;
        miesiac = miesiac - 20;
    }
    else
    {
        rok += 1900;
    }

    let dataUrodzenia = new Date();
    dataUrodzenia.setFullYear(rok, miesiac, dzien);

    // Weryfikacja numery PESEL
    let wagi = [9,7,3,1,9,7,3,1,9,7];
    let suma = 0;

    for(let i=0;i < wagi.length; i++)
    {
        suma+=(parseInt(pesel.substring(i,i+1),10)*wagi[i]);
    }

    suma=suma % 10;

    var cyfraKontr = parseInt(pesel.substring(10,11),10);
    var poprawnosc = (suma === cyfraKontr);

    //określenie płci
    var plec = 'k';

    if(parseInt(pesel.substring(9,10),10) % 2 === 1)
    {
        plec = 'm';
    }

    return {
        valid: poprawnosc,
        sex: plec,
        date: dataUrodzenia
    };
}

$.validator.methods.pesel = function( value, element )
{
return this.optional( element ) || (rozkodujPesel(value).valid && kontroladigit(value)&& kontroladlugosci11(value)) ;  
};

$.validator.addMethod( "polskieznaki", function( value, element ) {
	return this.optional( element ) || /^[a-ząśżźćńęłó ]+$/i.test( value );
}, "Tylko polskie znaki" );


