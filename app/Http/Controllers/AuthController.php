<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Candidato;
use App\Models\TipoCandidato;
use App\Models\Voto;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Info(
 *    title="API Prueba",
 *    version="1.0.0",
 * ),
 *   @OA\SecurityScheme(
 *       securityScheme="bearerAuth",
 *       in="header",
 *       name="bearerAuth",
 *       type="http",
 *       scheme="bearer",
 *       bearerFormat="JWT",
 *    ),
 */
/**
 * @OA\Schema(
 *     schema="CreateUserRequest",
 *     required={"name", "email", "password"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
/**
 * @OA\Schema(
 *     schema="LoginUserRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string"),
 * )
 */
class AuthController extends Controller
{
     /**
 *  @OA\Post(
 *     path="/api/auth/register",
 *     summary="Crear un nuevo usuario",
 *     description="Este endpoint se utiliza para crear un nuevo usuario junto con su información de persona asociada en la aplicación.",
 *     operationId="createUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CreateUserRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuario creado exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User Created Successfully"),
 *             @OA\Property(property="token", type="string", example="API TOKEN")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Campos vacíos o inválidos",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Existen campos vacios"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function createUser(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Existen campos vacios',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     summary="Iniciar sesión de usuario",
 *     description="Este endpoint se utiliza para permitir a un usuario iniciar sesión en la aplicación.",
 *     operationId="loginUser",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/LoginUserRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuario ha iniciado sesión exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User Logged In Successfully"),
 *             @OA\Property(property="token", type="string", example="API TOKEN")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Error de validación o Email y contraseña no coinciden con nuestros registros",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
    /**
 * @OA\Get(
 *     path="/api/auth/lista/candidatos",
 *     summary="Obtener lista de candidatos",
 *     description="Este endpoint se utiliza para obtener un listado de candidatos",
 *     operationId="listacandidatos",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de candidatos obtenida exitosamente",
 *       
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function listacandidatos(){
        $candidatos = DB::table('candidatos')
                    ->join('listas', 'candidatos.lista_id', '=', 'listas.id')
                    ->select('candidatos.nombre', 'listas.nombre as lista', 'candidatos.tipo')
                    ->get();
    
          return response()->json([
            "Listado"=> $candidatos,
          ]);
          
    }
/**
 * @OA\Get(
 *     path="/api/auth/lista/list",
 *     summary="Obtener lista de candidatospor nombre",
 *     description="Este endpoint se utiliza para obtener un listado de candidatos por nombres",
 *     operationId="list",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de candidatos por nombre  obtenida exitosamente",
 *       
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function List(){
        $candidatosList = DB::table('candidatos')
        ->join('votos', 'candidatos.id', '=', 'votos.candidato_id')
        ->select('candidatos.nombre', DB::raw('SUM(votos.total) as total_votos'))
        ->groupBy('candidatos.id')
        ->get();

        return response()->json([
            "Listado"=> $candidatosList,
          ]);    }
/**
 * @OA\Get(
 *     path="/api/auth/lista/ingreso",
 *     summary="actulizar ",
 *     description="Este endpoint se utiliza para obtener un listado de candidatos por nombres",
 *     operationId="list",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de candidatos por nombre  obtenida exitosamente",
 *       
 *     @OA\Response(
 *         response=500,
 *         description="Error del servidor",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string")
 *         )
 *     )
 * )
 */
    public function ingreso(Request $request){
       $voto = new Voto();
    $voto->candidato_id = $request->input('candidato_id');
    $voto->total = $request->input('total');
    $voto->save();

    return redirect()->route('votos.create')->with('success', 'Voto ingresado correctamente');   
    }

    public function updateCandidatos(Request $request, $id){
       // Validar los datos enviados desde el formulario
       $request->validate([
        'descripcion' => 'required|string',
       'candidato_id' => 'required', 
        ]);

        $candidato = Candidato::find($id);


      $candidato->update([
       'candidato' => $request->input('candidato'),
       'tipocandidato_id' => $request->input('tipocandidato_id'),

        ]);
  


      return response()->json(['message' => ' actualizado correctamente']);
    }
    public function eliminarCandidato(Request $request, $id){
        {
            // Buscar  por su ID
            $candidatoE = Candidato::find($id);
        
            // Verificar si el  existe
            if ($candidatoE->estado==false) {
                return response()->json(['message' => 'Candidato no encontrado'], 404);
            }
        
            // Obtener 
            $tipocandidato = $candidatoE->tipocandidato;
        
            // Eliminar 
            foreach ($tipocandidato as $tipocandidato) {
                $tipocandidato->estado=false;
                $tipocandidato->save();
                
            }
        
            // Devolver una respuesta de éxito
            return response()->json(['message' => 'candidato eliminado correctamente'],200);
        } 
    }
}
