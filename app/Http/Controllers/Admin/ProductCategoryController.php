<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ProductCategoryController extends Controller
{
    public function productCategory()
    {
        $common_data = new Array_();
        $common_data->title = 'Product Category';
        $category = ProductCategory::where('status', 1)->where('deleted', 0)->get();
        return view('adminPanel.product_category.product_category')->with(compact('category', 'common_data'));
    }

    public function productCategoryStore(Request $request)
    {
        $category = new ProductCategory();
        $category->name = $request->name;
        $category->note = $request->note;
        $category->image = $this->categoryIcon($request->banner_img);
        $category->created_at = Carbon::now();
        $category->save();
        return redirect()->back()->with('success', 'Successfully Added Category');
    }

    public function productCategoryUpdate(Request $request)
    {

        $subcategory = ProductCategory::find($request->category_id);
        $subcategory->name = $request->name;
        $subcategory->note = $request->note;
        if ($request->updateImage) {
            $subcategory->image = $this->categoryIcon($request->updateImage);
        }
        if ($request->is_popular) {
            $subcategory->is_popular = 1;
        } else {
            $subcategory->is_popular = 0;
        }
        $subcategory->save();
        return redirect()->back()->with('success', 'Category Successfully Updated');
    }
    public function productCategoryDelete(Request $request)
    {
        $subcategory = ProductCategory::find($request->id);
        $subcategory->deleted = 1;
        $subcategory->save();
        return redirect()->back()->with('success', 'Category Successfully Deleted');
    }


    public function categoryIcon($image)
    {
        if (isset($image) && ($image != '') && ($image != null)) {
            $ext = explode('/', mime_content_type($image))[1];

            $logo_url = "category_icons-" . time() . rand(1000, 9999) . '.' . $ext;
            $logo_directory = getUploadPath() . '/category_icons/';
            $filePath = $logo_directory;
            $logo_path = $filePath . $logo_url;
            $db_media_img_path = 'storage/category_icons/' . $logo_url;

            if (!file_exists($filePath)) {
                mkdir($filePath, 777, true);
            }

            $manager = new ImageManager(new Driver());
            $logo_image = $manager->read(file_get_contents($image));
            $encode_logo = $logo_image->encode(new WebpEncoder(quality: 70));
            $encode_logo->save($logo_path);

            return $db_media_img_path;
        }
    }
}
