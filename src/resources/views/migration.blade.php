<?php echo '<?php' ?>


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class {{ $class }} extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema to create roles table
        Schema::create('{{ $entrust['tables']['roles'] }}', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Schema to create permissions table
        Schema::create('{{ $entrust['tables']['permissions'] }}', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        
        // Schema to create role_users table
        Schema::create('{{ $entrust['tables']['role_user'] }}', function (Blueprint $table) {
            $table->unsignedBigInteger('{{ $entrust['foreign_keys']['role'] }}');
            $table->unsignedBigInteger('{{ $entrust['foreign_keys']['user'] }}');

            $table->foreign('{{ $entrust['foreign_keys']['user'] }}')->references('id')->on('{{ $entrust['user_table'] }}')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('{{ $entrust['foreign_keys']['role'] }}')->references('id')->on('{{ $entrust['tables']['roles'] }}')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['{{ $entrust['foreign_keys']['user'] }}', '{{ $entrust['foreign_keys']['role'] }}']);
        });

        // Schema to create permission_role table
        Schema::create('{{ $entrust['tables']['permission_role'] }}', function (Blueprint $table) {
            $table->unsignedBigInteger('{{ $entrust['foreign_keys']['permission'] }}');
            $table->unsignedBigInteger('{{ $entrust['foreign_keys']['role'] }}');

            $table->foreign('{{ $entrust['foreign_keys']['permission'] }}')->references('id')->on('{{ $entrust['tables']['permissions'] }}')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('{{ $entrust['foreign_keys']['role'] }}')->references('id')->on('{{ $entrust['tables']['roles'] }}')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->primary(['{{ $entrust['foreign_keys']['permission'] }}', '{{ $entrust['foreign_keys']['role'] }}']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $entrust['tables']['permission_role'] }}');
        Schema::dropIfExists('{{ $entrust['tables']['role_user'] }}');
        Schema::dropIfExists('{{ $entrust['tables']['permissions'] }}');
        Schema::dropIfExists('{{ $entrust['tables']['roles'] }}');
    }
}