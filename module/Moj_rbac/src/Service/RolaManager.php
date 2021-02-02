<?php

namespace Moj_rbac\Service;

use Moj_rbac\Model\Rola;
use Laminas\Db\ResultSet\HydratingResultSet;
use Moj_rbac\Polaczenie\RbacPolaczenie;

class RolaManager 
{
    
    /**
     * Metaoda zamienia wyniki z bazy danych na atablice opcji do wpisania dla kontrolki multicheckbox
     * formularza RolaForm, w celu wybrania ról/roli do dziedziczenia
     * @param HydratingResultSet $role
     * @return array
     */
    public function zamienObiektyRolaNaTabliceOptions(HydratingResultSet $role) : array
    {
     //   $tablicaOpcji=array();
        foreach ($role as $rola)
        {
           $tablicaOpcji[] = [
             'value'=>$rola->getIdrola(),
             'label'=>$rola->getName(),
            ];
         //   $tablicaOpcji[$rola->getIdrola()]=$rola->getName();
        }


        return $tablicaOpcji;
    }
    /**
     * Metaoda zamienia wyniki z bazy danych na atablice opcji do wpisania dla kontrolki multicheckbox
     * formularza RolaFormEdit, w celu wybrania ról/roli do dziedziczenia
     * 
     * @param HydratingResultSet $role
     * @param array $tablicaDzieckoRolaId
     * @param Rola $rola
     * @return array
     */
    public function zamienRoleNatabliceOptions(
            HydratingResultSet $role, 
            array $tablicaDzieckoRolaId,
            Rola $rola): array
    {
          $tablicaOpcji=array();
         
         
          
          foreach ($role as $pojRola)
          {
              if($pojRola->getName()!==$rola->getName())
            {     
               $selected=false;
               
              foreach ($tablicaDzieckoRolaId as $id)
              {
                  if($id==$pojRola->getIdrola())
                  {
                      $selected=true;
                  }
              }
              
              $label_attributes=$pojRola->getName().'_label';
              $tablicaOpcji[]=
              [
                'value'=>$pojRola->getIdrola(),
                'label'=>$pojRola->getName(),
                 'selected' => $selected, 
                  'attributes' => [
                //    'id' => $pojRola->getName(),
                      'id'=>'multi_checkbox',
                    'data-fruit' => 'apple',
                ],
                'label_attributes' => [
                    'id' => $label_attributes,
                ],
              ];
             }
          }
          
        return $tablicaOpcji;
    }
    
    public function updateRola(RbacPolaczenie $polaczenie, array $dane, Rola $rola)
    {
      
       $transakcja=$polaczenie->getAdapter();
       $transakcja->getDriver()->getConnection()->beginTransaction(); 
       
       try{
           if($dane['checkbox']==='Tak'){
               $rola=$polaczenie->aktualizujRola_bezRola_hierarchia($dane, $rola);
           }
       } catch (Exception $ex) {
            $transakcja->getDriver()->getConnection()->rollback();
            return -1;
       }
        
       
       try{
           if($dane['checkbox']==='Nie' && is_array($dane['multi_checkbox'])){
                $rola=$polaczenie->aktualizujRola_bezRola_hierarchia($dane, $rola);
                $polaczenie->aktualizujRola_zRola_hierarchia($dane['multi_checkbox'],$rola);
           }else{
             $rola=$polaczenie->aktualizujRola_bezRola_hierarchia($dane, $rola);  
           }
       } catch (Exception $ex) {
           $transakcja->getDriver()->getConnection()->rollback();
            return -1;
       }

       $transakcja->getDriver()->getConnection()->commit();
       
       return 1;
    }
    
    
    public function wyliczRoleDzieci($idRola,$tablicaDzieci,$polaczenie): array
    {
     
     $wyniki=$polaczenie->pobierzRoleZRole_hierarchia($idRola);
       
       if($wyniki){
       foreach ($wyniki as $jednodziecko)
       {
           $tablicaDzieci[$jednodziecko]['idrola']=$jednodziecko;
           $tablicaDzieci[$jednodziecko]['dziedziczony']='[dziedziczony]';
           
          $tablicaDzieci=$this->wyliczRoleDzieci_wokol($jednodziecko, $tablicaDzieci, $polaczenie);
       }
       }   
        
       return $tablicaDzieci;
    }
    
    protected function wyliczRoleDzieci_wokol($id,$tablica,$polaczenie)
    {
      $wyniki=$polaczenie->pobierzRoleZRole_hierarchia($id);
       
       if($wyniki){
       foreach ($wyniki as $jednodziecko)
       {
           $tablica[$jednodziecko]['idrola']=$jednodziecko;
           $tablica[$jednodziecko]['dziedziczony']='[dziedziczony]';
           
           if($jednodziecko)
           {
             $tablica=$this->wyliczRoleDzieci_wokol($jednodziecko, $tablica, $polaczenie);
           }
           
       }
       }  
       
       return $tablica;
    }
    
}
