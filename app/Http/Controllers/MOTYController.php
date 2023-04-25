<?php
namespace App\Http\Controllers;

use App\Http\Services\Respond;
use App\Models\MOTYNominee;
use Illuminate\Http\Request;

/**
 * Class MOTYController
 * @package App\Http\Controllers
 */
class MOTYController extends Controller
{
    protected $year = 2022;

    public function saveNominee(Request $request)
    {
        if ($request->post('id', 0))
            $nominee = MOTYNominee::where('id', $request->post('id'))->first();
        else
            $nominee = new MOTYNominee();

        $nominee->name = $request->post('name');
        $nominee->description = json_encode(json_decode($request->getContent())->description);
        $nominee->save();

        Respond::success("Nominee saved successfully!");
    }

    public function deleteNominee(MOTYNominee $nominee)
    {
        $nominee->delete();
        Respond::success("Nominee deleted successfully!");
    }

    public function getNominees()
    {
        Respond::success("Nominees fetched successfully!", MOTYNominee::whereRaw("YEAR(updated_at)={$this->year}") ->get());
    }
}
