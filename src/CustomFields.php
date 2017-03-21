<?php
namespace Webmachine\CustomFields;

use Illuminate\Support\Facades\DB;
use Webmachine\Form\FormFacade as Form;

class CustomFields {

    /**
     * Genera el html para los campos personalizados de una tabla dada
     * 
     * @param string $table nombre de la tabla
     * @param string $form_scope ambito en el que se utiliza el campo
     * @return string
     */
    public function show($table, $form_scope = ''){
        $result = '';
        $custom_fields = $this->getCustomFields($table);
        foreach($custom_fields as $f) {
            if(!$this->belogsToScope($f, $form_scope)) continue;
            $name = strtolower("cf[{$f->name}_{$f->id}]");
            $id = strtolower("cf.{$f->name}_{$f->id}");
            $attributes = empty($f->attributes)? ['label' => $f->name, 'id' => $id] : array_merge(['label' => $f->name, 'id' => $id], unserialize($f->attributes));
            
            if($f->type == 'text') {
                $result .= Form::text($name, $attributes);
            } else if($f->type == 'textarea') {
                $result .= Form::textarea($name, $attributes);
            } else if($f->type == 'select') {
                $result .= Form::select($name, unserialize($f->options), $attributes);
            }
        }
        return $result;
    }

    /**
     * Guarda en base de datos campos personalizados
     * 
     * @param int $registro_id id del registro al que se le agregan los valores de los campos personalizados
     * @return boolean
     */
    public function save($registro_id) {
        $custom_field_values = [];
        $custom_fields = request('cf') === NULL? [] : request('cf');
        foreach($custom_fields as $id => $value){
            if(empty($value)) continue;
            list(,$campo_extra_id) = explode('_', $id);
            $custom_field_values[] = [
                'custom_field_id' => $campo_extra_id,
                'record_id' => $registro_id,
                'value' => $value
            ];
        }      
        return empty($custom_field_values)? FALSE : DB::table('custom_field_values')->insert($custom_field_values);     
    }
    
    /**
     * Obtiene arreglo con reglas de validación de campos personalizados
     * 
     * @param string $table nombre de la tabla
     * @param string $form_scope ambito en el que se utiliza el campo
     * @return array
     */
    public function rules($table, $form_scope = '') {
        $rules = [];
        $custom_fields = $this->getCustomFields($table);
        foreach($custom_fields as $f) {
            if(!$this->belogsToScope($f, $form_scope)) continue;
            $name = strtolower("cf.{$f->name}_{$f->id}");
            $rules[$name] = $f->validation_rules;
        }
        return $rules;
    }
   
    /**
     * Obtiene arreglo con nombres de campos personalizados
     * 
     * @param string $table nombre de la tabla
     * @param string $form_scope ambito en el que se utiliza el campo
     * @return array
     */    
    public function attributes($table, $form_scope = '') {
        $attributes = [];
        $custom_fields = $this->getCustomFields($table);
        foreach($custom_fields as $f) {
            if(!$this->belogsToScope($f, $form_scope)) continue;
            $name = strtolower("cf.{$f->name}_{$f->id}");
            $attributes[$name] = $f->name;
        }
        return $attributes;
    }
        
    /**
     * Determina si el campo pertenece al ambito consultado
     * 
     * @param object stdClass $field el nombre del campo
     * @param string $form_scope ambito en el que se utiliza el campo
     * @return boolean
     */
    protected function belogsToScope($field, $form_scope) {
        $belongs = empty($form_scope) || empty($field->form_scope)? TRUE : in_array($form_scope, explode('|', $field->form_scope));
        return $belongs;
    }
    
    /**
     * Obtiene campos personalizados de una tabla
     * 
     * @param string $table nombre de la tabla
     * @return array
     */
    public function getCustomFields($table) {
        return DB::table('custom_fields')->where('table', $table)->orderBy('order', 'asc')->get();
    }    

}