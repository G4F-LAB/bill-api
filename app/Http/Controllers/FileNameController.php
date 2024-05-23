<?php

namespace App\Http\Controllers;

use App\Models\FileName;
use Illuminate\Http\Request;

class FileNameController extends Controller
{
    protected $allow_types = ['Admin', 'Operação', 'Executivo', 'Analista', 'RH', 'Financeiro'];

    public function __construct()
    {
        // Verifique se há um usuário autenticado
        if (auth()->check()) {
            // Acesse o tipo do usuário autenticado
            $userType = auth()->user()->type;

            // Verifique se o tipo de usuário está entre os tipos permitidos
            if (!in_array($userType, $this->allow_types)) {
                // Se o tipo de usuário não estiver entre os permitidos, retorne uma resposta de erro
                return response()->json(['error' => 'Acesso não permitido.'], 403);
            }
        } else {
            // Se não houver usuário autenticado, retorne uma resposta de erro
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    }

    public function index(Request $request)
    {
        // Retrieve file naming records
        $query = FileName::with(['type', 'task.integration']);

        // Apply filter if provided
        if ($request->has('q')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->q) . '%']);
        }

        $response = $query->orderBy('name', 'asc')->get();

        return response()->json($response, 200);
    }

    public function create(Request $request)
    {


        // Create a new file naming record
        $fileName = new FileName();
        $fileName->fill($request->all());
        $fileName->save();

        // Return success response
        return response()->json(['message' => 'Registro de nome de arquivo criado com sucesso'], 201);
    }

    public function update(Request $request, $id)
    {

        // Find the file naming record by id
        $fileName = FileName::find($id);

        // Check if the record exists
        if (!$fileName) {
            return response()->json(['error' => 'Registro de nome de arquivo não encontrado'], 404);
        }

        // Update the file naming record
        $fileName->fill($request->all());
        $fileName->save();

        // Return success response
        return response()->json(['message' => 'Registro de nome de arquivo atualizado com sucesso'], 200);
    }

    public function get($id)
    {
        // Retrieve the file naming record by id
        $fileName = FileName::find($id);

        // Check if the record exists
        if (!$fileName) {
            return response()->json(['error' => 'Registro de nome de arquivo não encontrado'], 404);
        }

        return response()->json($fileName, 200);
    }

    public function delete($id)
    {
        // Find the file naming record by id
        $fileName = FileName::find($id);

        // Check if the record exists
        if (!$fileName) {
            return response()->json(['error' => 'Registro de nome de arquivo não encontrado'], 404);
        }

        // Delete the file naming record
        $fileName->delete();

        // Return success response
        return response()->json(['message' => 'Registro de nome de arquivo excluído com sucesso'], 200);
    }
}
