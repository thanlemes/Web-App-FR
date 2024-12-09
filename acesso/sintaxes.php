<?php

namespace App\Http\Controllers\ServicoSmmu\Inteligencia;
use App\Models\NomeDaModel;


// ><><><><><><><><><><><><><><><><><><><><><><>< I N S E R T ><><><><><><><><><><><><><><><><><><><><><><><

// Exemplo de criação de registro com create()
// Certifique-se de que o modelo tenha $fillable ou $guarded configurado para proteção em massa.

// Modelo configurado
// protected $fillable = ['nome', 'email', 'telefone'];

$data = [
    'nome' => 'João Silva',
    'email' => 'joao@email.com',
    'telefone' => '123456789'
];

// Usando o modelo para inserir
User::create($data);

// Exemplo de criação com insert()
DB::table('NomeDaTabela')->insert([
    'nome' => 'Maria Oliveira',
    'email' => 'maria@email.com',
    'telefone' => '987654321',
    'created_at' => now(),
    'updated_at' => now()
]);

// ><><><><><><><><><><><><><><><><><><><><><><>< S E L E C T ><><><><><><><><><><><><><><><><><><><><><><><

// Selecionar todos os registros
$usuarios = User::all();

// Selecionar registros com select() e get()
$usuarios = DB::table('NomeDaTabela')
    ->select('id', 'nome', 'email')
    ->get();

// Selecionar com where (condições)
$usuarios = User::where('status', 'ativo')
    ->where('idade', '>', 18)
    ->get();

// Utilizar joins
$dados = DB::table('NomeDaTabela')
    ->join('departamentos', 'NomeDaTabela.departamento_id', '=', 'departamentos.id')
    ->select('NomeDaTabela.nome', 'departamentos.nome as departamento')
    ->get();

// Paginação
$usuarios = User::paginate(10);

// Exemplos de Consultas Avançadas
// Agrupamento com groupBy e agregação
$contagem = DB::table('NomeDaTabela')
    ->select(DB::raw('departamento_id, COUNT(*) as total'))
    ->groupBy('departamento_id')
    ->get();

// Ordenação com orderBy
$usuarios = User::orderBy('nome', 'asc')->get();

// Limitar resultados
$usuarios = User::limit(5)->get();

// Juntar múltiplas tabelas com join
$dados = DB::table('NomeDaTabela')
    ->join('departamentos', 'NomeDaTabela.departamento_id', '=', 'departamentos.id')
    ->join('cargos', 'NomeDaTabela.cargo_id', '=', 'cargos.id')
    ->select('NomeDaTabela.nome', 'departamentos.nome as departamento', 'cargos.nome as cargo')
    ->where('NomeDaTabela.status', 'ativo')
    ->get();

// ><><><><><><><><><><><><><><><><><><><><><><>< U P D A T E ><><><><><><><><><><><><><><><><><><><><><><><

// Atualizar um registro pelo modelo
$user = User::find(1);
$user->nome = 'João Atualizado';
$user->save();

// Atualizar com update()
User::where('id', 1)->update(['nome' => 'Nome Atualizado']);

// Usando DB para atualizar
DB::table('NomeDaTabela')
    ->where('id', 1)
    ->update(['email' => 'novoemail@email.com']);

// ><><><><><><><><><><><><><><><><><><><><><><>< D E L E T E ><><><><><><><><><><><><><><><><><><><><><><><

// Deletar pelo modelo
$user = User::find(1);
$user->delete();

// Deletar com destroy()
User::destroy(1); // Deleta pelo ID
User::destroy([1, 2, 3]); // Deleta múltiplos IDs

// Deletar com DB
DB::table('NomeDaTabela')->where('id', 1)->delete();