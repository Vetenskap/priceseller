<?php

namespace Modules\EditorContent\Livewire\EditorContent;

use App\HttpClient\OzonClient\Resources\DescriptionCategory;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryAttribute;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryAttributeValue;
use App\HttpClient\OzonClient\Resources\DescriptionCategoryTree;
use App\HttpClient\OzonClient\Resources\ProductInfoAttribute;
use App\Livewire\ModuleComponent;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;
use Livewire\WithFileUploads;
use Modules\EditorContent\Services\EditorContentService;

class EditorContentIndex extends ModuleComponent
{
    use WithFileUploads;

    public $article;
    public $cards = [];
    public $selectedCard;

    public $name;

    public $description;

    public $images = [];

    public $selectedCategories = [];

    public $currentAvailableCategories = [];

    public $categoryAttributes = [];
    public $categoryAttributesValues = [];

    public $categoryAttributesDictionarySearch = [];

    public $categoryAttributesDictionary;

    public $categoryAttributesValuesSelected = [];

    public $lastCategory;

    public $prevCategory;

    public function updatedCategoryAttributesDictionarySearch(): void
    {
        $this->categoryAttributesDictionary = collect($this->categoryAttributesDictionarySearch)->mapWithKeys(function ($value, $key) {

            if (strlen($value) >= 2) {

                $ozonMarket = OzonMarket::find('9d07c539-bf26-41cf-a00c-57cecb5f008b');
                $descriptionCategoryAttribute = new DescriptionCategoryAttribute();
                $descriptionCategoryAttribute->setDescriptionCategoryAttribute(collect(collect($this->categoryAttributes)->firstWhere('id', $key)));
                $descriptionCategoryAttribute->fetchValues($ozonMarket, $this->selectedCategories[count($this->selectedCategories) - 2], $this->selectedCategories[count($this->selectedCategories) - 1], $value);

                return [$key => $descriptionCategoryAttribute->getValues()->map(fn(DescriptionCategoryAttributeValue $value) => $value->toArray())->toArray()];

            }

            return [$key => []];

        })->toArray();


    }

    public function getDescriptionCategoryTree(): DescriptionCategoryTree
    {
        $ozonMarket = OzonMarket::find('9d07c539-bf26-41cf-a00c-57cecb5f008b');
        $descriptionCategoryTree = new DescriptionCategoryTree();
        $descriptionCategoryTree->fetch($ozonMarket);

        return $descriptionCategoryTree;
    }

    public function mount()
    {
        parent::mount();

        $descriptionCategoryTree = $this->getDescriptionCategoryTree();

        $this->currentAvailableCategories[0] = $this->getCurrentAvailableCategoriesFromTree($descriptionCategoryTree)->toArray();
        $this->selectedCategories[0] = ['description_category_id' => null, 'category_name' => 'Нет'];
    }

    public function findCategoryById(int $id, bool $typeId, DescriptionCategoryTree|DescriptionCategory|null $category)
    {
        if (!$category) return null;

        return match (get_class($category)) {
            DescriptionCategory::class => $typeId ? ($category->getTypeId() === $id ? $category : $this->findCategoryById($id, true, $category->getChildren())) : ($category->getDescriptionCategoryId() === $id ? $category : $this->findCategoryById($id, false, $category->getChildren())),
            DescriptionCategoryTree::class => $category->getDescriptionCategories()->map(fn(DescriptionCategory|DescriptionCategoryTree $category) => $this->findCategoryById($id, $typeId, $category))->first(fn($category) => $category),
        };

    }

    public function getCurrentAvailableCategoriesFromCategory(DescriptionCategory $descriptionCategory): Collection
    {
        return match (get_class($descriptionCategory->getChildren())) {
            DescriptionCategory::class => $descriptionCategory->getChildren()->getDescriptionCategoryId() ? ['description_category_id' => $descriptionCategory->getChildren()->getDescriptionCategoryId(), 'category_name' => $descriptionCategory->getChildren()->getCategoryName()] : ['type_id' => $descriptionCategory->getChildren()->getTypeId(), 'type_name' => $descriptionCategory->getChildren()->getTypeName()],
            DescriptionCategoryTree::class => $this->getCurrentAvailableCategoriesFromTree($descriptionCategory->getChildren())
        };
    }

    public function getCurrentAvailableCategoriesFromTree(DescriptionCategoryTree $descriptionCategoryTree): Collection
    {
        return $descriptionCategoryTree->getDescriptionCategories()->map(function (DescriptionCategory|DescriptionCategoryTree $category) {
            return match (get_class($category)) {
                DescriptionCategory::class => $category->getDescriptionCategoryId() ? ['description_category_id' => $category->getDescriptionCategoryId(), 'category_name' => $category->getCategoryName()] : ['type_id' => $category->getTypeId(), 'type_name' => $category->getTypeName()],
                DescriptionCategoryTree::class => $this->getCurrentAvailableCategoriesFromTree($category)
            };
        });
    }

    public function updatedSelectedCategories($value, $index)
    {
        $this->categoryAttributes = [];

        $index = intval($index);
        // Очистим последующие уровни при изменении текущего уровня
        $this->selectedCategories = array_slice($this->selectedCategories, 0, $index + 1);

        $descriptionCategoryTree = $this->getDescriptionCategoryTree();

        $id = $this->selectedCategories[$index];
        $typeId = (bool) collect($this->currentAvailableCategories)->map(function (array $category) use ($id) {
            return collect($category)->firstWhere('type_id', $id);
        })->filter()->first();

        // Найдем текущую категорию
        /** @var DescriptionCategory $currentCategory */
        $currentCategory = $this->findCategoryById($id, $typeId, $descriptionCategoryTree);

        if ($currentCategory) {

            // Если есть дочерние категории, загрузим их на следующий уровень
            if (!is_null($currentCategory->getChildren())) {
                $this->selectedCategories[$index + 1] = ['description_category_id' => null, 'category_name' => 'Нет'];
                $this->currentAvailableCategories[$index + 1] = $this->getCurrentAvailableCategoriesFromCategory($currentCategory)->toArray();
            } else {
//
                $ozonMarket = OzonMarket::find('9d07c539-bf26-41cf-a00c-57cecb5f008b');
                $currentCategory->fetchAttributes($ozonMarket, $this->selectedCategories[$index - 1]);

                $this->categoryAttributes = $currentCategory->getAttributes()->map(fn(DescriptionCategoryAttribute $attribute) => $attribute->toArray())->toArray();
            }
        }
    }

    public function save()
    {
        // Логика для сохранения изображений
        $this->validate([
            'images' => 'required|array|min:1|max:12',
            'images.*' => 'image|max:1024',
            'lastCategory' => 'required',
        ], [
            'lastCategory.required' => 'Выбраны не все категории',
        ]);

        foreach ($this->images as $image) {
            $image->store('photos', 'public');
        }

        \Flux::toast('Изменения успешно отправлены!', 'Уведомление');
    }

    // Метод для поиска карточек по API
    public function search()
    {
        $service = new EditorContentService(auth()->user());
        $cards = $service->getOzonProductsInfo($this->article);
        $this->cards = $cards->map(fn(ProductInfoAttribute $product) => $product->toCollection())->toArray();
    }

    // Метод для выбора карточки
    public function selectCard($cardId)
    {
        $this->selectedCard = collect($this->cards)->firstWhere('id', $cardId);
    }


    public function render()
    {
        return view('editorcontent::livewire.editor-content.editor-content-index', [
            'modules' => $this->getEnabledModules()
        ]);
    }
}
