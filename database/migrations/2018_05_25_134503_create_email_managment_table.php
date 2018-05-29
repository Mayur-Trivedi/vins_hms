<?php

use Illuminate\Support\Facades\Schema;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Database\Migrations\Migration;  
   
class CreateEmailManagmentTable extends Migration  
{  
    /**    
     * Run the migrations. 
     * 
     * @return void    
     */    
    public function up()   
    {  
        Schema::create('email_managment', function (Blueprint $table) {    
            $table->increments('id');  
            $table->string('type')->nullable();    
            $table->string('title')->nullable();   
            $table->longText('content')->nullable();   
            $table->boolean('status'); 
            $table->timestamps();  
        });    
    }  
   
    /**    
     * Reverse the migrations. 
     * 
     * @return void    
     */    
    public function down() 
    {  
        Schema::dropIfExists('email_managment');   
    }
}