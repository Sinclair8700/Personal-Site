<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Education;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('image');
            $table->string('description');
            $table->string('location');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('link');
            $table->timestamps();
        });

        Education::create([
            'name' => 'Thursfield Primary School', 
            'slug' => 'thursfield-primary-school', 
            'image' => 'thursfield.jpg', 
            'description' => 'I attended Thursfield Primary School from 2005 to 2012.', 
            'location' => 'Thursfield, Lancashire', 
            'start_date' => '2005-09-01', 
            'end_date' => '2012-07-15', 
            'link' => 'https://www.ucsb.edu/']);

        Education::create([
            'name' => 'Newcastle-under-Lyme School', 
            'slug' => 'newcastle-under-lyme-school', 
            'image' => 'newcastle.jpg', 
            'description' => 'I attended Newcastle-under-Lyme School from 2012 to 2019.', 
            'location' => 'Newcastle-under-Lyme, Staffordshire', 
            'start_date' => '2012-09-01', 
            'end_date' => '2019-07-15', 
            'link' => 'https://www.newcastle-under-lyme.sch.uk/'],
        );

        Education::create([
            'name' => 'Keele University', 
            'slug' => 'keele-university', 
            'image' => 'keele.jpg', 
            'description' => 'I studied Computer Science at Keele University from 2019 to 2022.', 
            'location' => 'Newcastle-under-Lyme, Staffordshire', 
            'start_date' => '2019-09-01', 
            'end_date' => '2022-06-15', 
            'link' => 'https://www.keele.ac.uk/'],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};
