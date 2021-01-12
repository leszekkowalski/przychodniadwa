<?php

namespace Application\Service;

use Application\Polaczenie\UzytkownikPolaczenie;
use Laminas\View\Renderer\PhpRenderer;
use Application\Model\Uzytkownik;
use Exception;
use Laminas\Math\Rand;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mail;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;


class UzytkownikManager
{
    /**
     *
     * @var type Application\Polaczenie\UzytkownikPolaczenie;
     */
    public $uzytkownikDb;
    /**
     *
     * @var type Laminas\View\Renderer\PhpRenderer;
     */
    protected $viewRenderer;
    /**
     *
     * @var type Application array config 
     */
    protected $config;
    /**
     *
     * @var type Application\Model\Uzytkownik;
     */
    protected $uzytkownik;


    public function __construct(UzytkownikPolaczenie $db, PhpRenderer $render, $config)
    {
      $this->uzytkownikDb=$db;  
      $this->viewRenderer=$render;
      $this->config=$config;
    }
    
    public function znajdzUzytkownikPoMail($mail)
    {
      $uzytkownik=$this->uzytkownikDb->znajdzJedenPoMailUzytkownik($mail);
      return $uzytkownik;
    }
    //dodaje Uzytkownika
    public function dodajUzytkownik(Uzytkownik $uzytkownik)
    {
        if($this->uzytkownik)
        {
            throw new Exception('Nie możnw wpisać ponownie uzytkownika');
        }else
        {
            $this->uzytkownik=$uzytkownik;
        }
    }
    // tworzy pole pwd_sol w celu wygenerowania specjalnego tokenu, (szyfruję je)  i daty jego powstania, zapisu w bazie danych
    // i na ich podstawie wysłania maila na wpisany mail w celu umozliwienia resetu hasła do konta uzytkownika
    public function tworzPwd_sol_wyslijMaila($baseUrl) 
    {
        
       $token=Rand::getString(32, '0123456789qwertyuiopasdfghjklzxcvbnm');
       
       $token='654786gfdrewsvbn8904efgj1276aoqw';//wpisano dla testów - usunac dla wersji produkcyjnej
       
       $szyfrowanie= new Bcrypt();
       
        $pwd_sol=$szyfrowanie->create($token);
       $czas= date('Y-m-d H:i:s');
       
        $this->uzytkownik->setPwdSol($pwd_sol);
        $this->uzytkownik->setPwdSolData($czas);
        //wpisuje do bazy danych 
       $odbior=$this->uzytkownikDb->wpiszPwd_sol_pwd_sol_date($this->uzytkownik);
       
        if(!$odbior instanceof Uzytkownik){
            throw new \Exception('Błąd podczas aktualizacji ......uzytkownika');
        }
        
        //wysyłanie maila do Uzytkownika
        $temat='Reset hasła';

      $httpHost= isset($_SERVER['HTTP_HOST'])? $_SERVER['HTTP_HOST']: 'localhost';
      
      $UrlZmianaHaslo='http://'.$httpHost.$baseUrl.'/ustaw-haslo?token='.$token.'&mail='.$this->uzytkownik->getMail();
//  ustaw-haslo?token=654786gfdrewsvbn8904efgj1276aoqw&mail=stepien@wp.pl     
     $bodyHtml=$this->viewRenderer->render(
             'application/uzytkownik/mail/zmiana-haslo-mail',
             ['UrlZmianaHaslo'=>$UrlZmianaHaslo,'uzytkownik'=>$this->uzytkownik]
             );
     $html=new MimePart($bodyHtml);
     $html->type='text/html';
     
     $body=new MimeMessage();
     $body->addPart($html);
     
     $mail=new Mail\Message();
     $mail->setEncoding('utf-8');
     $mail->setBody($body);
     $mail->setFrom('no-reply@przychodniaLeszka.pl',' Użytkownik Demo');
     $mail->setTo($this->uzytkownik->getMail(), $this->uzytkownik->getImie().' '.$this->uzytkownik->getNazwisko());
     $mail->setSubject($temat);
     
      // Ustawiam SMTP transport
     $transport=new SmtpTransport();
     $options=new SmtpOptions($this->config['smtp']);
     
     $transport->setOptions($options);
     // ponizej zakomentowano dla wersji testowej - uzunac dla wersji produkcyjnej
    // try {
       //   $transport->send($mail);
    // } catch (Exception $ex) {
     //    print_r($ex);
    // }
    
    }
    /**
     * 
     * @param type $token
     * @param type $mail
     * @return int
     * metoda wyciaga z bazy Uzytkownika o wskazanym mailu, sprwadza czy token podany w linku 
     * przekierowania jest zgodny z tym zapisanym w bazie danych i daje odpowiednia informację 
     */
    public function kontrolaHasloJakoToken($token,$mail) 
    {
       $uzytkownik=$this->uzytkownikDb->znajdzJedenPoMailPwd_solUzytkownik($mail);
       
       $pwd_solBaza=$uzytkownik->getPwdSol();
      
       $szyfrowanie=new Bcrypt();
      // if(!(strcmp($pwd_solBaza, $token))==0)
      if(!$szyfrowanie->verify($token, $pwd_solBaza))
       {
        return -1;// hasła się nie zgadzają
       }
       
      $pwd_sol_dateBaza=$uzytkownik->getPwdSolData();
      
      $pwd_sol_dateBaza= strtotime($pwd_sol_dateBaza);
       
      $currentDate = strtotime('now');
        
     if ($currentDate - $pwd_sol_dateBaza > 24*60*60) 
     {
            return -2; // mineła doba
     }
       
    return 1;//wszystko OK
                     
    }
    /**
     * 
     * @param type $token
     * @param type $mail
     * @param type $haslo
     * @return boolean
     * Funkcja ponownie sparwdza token, pobiera Uzytkownia w wskazanym mailu
     * i wpisuje nowe hasło do Bazy 
     */
    public function wpiszNoweHasloPrzezToken($token,$mail, $haslo) {
       
        // sprawdzam ponownie token, mail i czas 
        if($this->kontrolaHasloJakoToken($token, $mail)!=1){
            return false;
        }
        
        //pobieram uzytkownika z bazy danych
        $uzytkownik=$this->uzytkownikDb->znajdzJedenPoMailUzytkownik($mail);
        //sprwadzam go ponownie
        if($uzytkownik==null || $uzytkownik->getStatusId()!= Uzytkownik::STATUS_AKTYWNY)
        {
            return false;
        }
        
        $szyfrowanie=new Bcrypt();
        
        $noweHaslo=$szyfrowanie->create($haslo);
        
        if($this->uzytkownikDb->wpiszResetHaslo($noweHaslo,$uzytkownik->getIduzytkownik()))
        {
          return true;  
        }else{
            return false;//blad
        }
        
        
    }
}

