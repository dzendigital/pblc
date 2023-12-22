<?php

namespace App\Http\Controllers\Client\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\BlogRepository as ItemRepository;
use App\Models\Blog\Item as Item;

class ItemController extends Controller
{
    public function service($slug)
    {
        $data = array();
        switch ($slug) {
            case 'inspection':
                $data = array(
                    "h1" => "Подбор под ключ",
                    "form" => "по услуге подбора под ключ",
                    "h2" => "Подбор автомобиля по вашим предпочтениям. Ищем автомобиль не только по общедоступным базам, но также в закрытых каналах, трейд-ин, у официальных дилеров, на специальных площадках.",
                    "ul" => array(
                        "Поиск по всем базам",
                        "Подбор автомобиля до желаемого результата",
                        "Юридическая проверка",
                        "Техническая проверка",
                        "Криминалистическая проверка",
                        "Полное сопровождение сделки, <br/> включая составление Договора купли-продажи",
                        "Сопровождение в постановке на учёт",
                    ),
                    "money" => "Стоимость 25 000 рублей",
                    "images" => array(
                        "lg" => "/resources/img/service/inspection.png",
                        "md" => "/resources/img/service/inspection-d.png",
                    ),
                );
            break;
            case 'once':
                $data = array(
                    "h1" => "Разовый осмотр",
                    "form" => "по услуге сервисного осмотра",
                    "h2" => "Выезд эксперта для осмотра выбранного вами автомобиля.",
                    "ul" => array(
                        "Юридическая проверка",
                        "Техническая проверка",
                        "Криминалистическая проверка",
                        "Помощь в заключении Договора купли-продажи",
                        "Диагностический лист на фирменном бланке",
                    ),
                    "money" => "Стоимость 5 000 рублей",
                    "images" => array(
                        "lg" => "/resources/img/service/once.png",
                        "md" => "/resources/img/service/once-d.png",
                    ),
                );
            break;
            case 'expert':
                $data = array(
                    "h1" => "Эксперт на день",
                    "form" => "по услуге эксперта на день",
                    "h2" => "Проверка до 4 выбранных вами автомобилей за день.",
                    "ul" => array(
                        "Юридическая проверка",
                        "Техническая проверка",
                        "Криминалистическая проверка",
                        "Помощь в заключении Договора купли-продажи",
                        "Диагностический лист на фирменном бланке",
                    ),
                    "money" => "Стоимость 14 000 рублей",
                    "images" => array(
                        "lg" => "/resources/img/service/expert.png",
                        "md" => "/resources/img/service/expert-d.png",
                    ),
                );
            break;
            case 'dostavka':
                $data = array(
                    "h1" => "Доставка автомобиля",
                    "form" => "по услуге доставки автомобиля",
                    "h2" => "В нашем парке несколько десятков автовозов. Мы сотрудничаем с ведущими транспортными компаниями, а так же наша организация владеет собственным парком автовозов, поэтому с легкостью доставим автомобиль в ваш город.",
                    "money" => "Стоимость 10 000 рублей",
                    "images" => array(
                        "lg" => "/resources/img/service/dostavka.png",
                        "md" => "/resources/img/service/dostavka-d.png",
                    ),
                );
            break;
            case 'buyout':
                $data = array(
                    "h1" => "Выкуп автомобиля",
                    "form" => "по услуге выкупа автомобиля",
                    "h2" => "Вы можете сдать свой автомобиль и получить скидку на новый. У нас большой выбор новых автомобилей и автомобилей с пробегом. Все они проверены экспертами и технически полностью исправны.",
                    "money" => "Оплата договорная",
                    "images" => array(
                        "lg" => "/resources/img/service/buyout.png",
                        "md" => "/resources/img/service/buyout-d.png",
                    ),
                );
            break;
            case 'commission':
                $data = array(
                    "h1" => "Комиссионная продажа",
                    "form" => "по услуге комиссионной продажи автомобиля",
                    "h2" => "Принимаем автомобили на комиссию по вашей цене. После продажи вы гарантировано получите сумму, на которую рассчитывали.",
                    "money" => "честные 5% от сделки",
                    "images" => array(
                        "lg" => "/resources/img/service/inspection.png",
                        "md" => "/resources/img/service/inspection-d.png",
                    ),
                );
            break;
            case 'traid-in':
                $data = array(
                    "h1" => "Трейд-ин",
                    "form" => "по услуге трейд-ин",
                    "h2" => "Вы можете сдать свой автомобиль и получить скидку на новый. У нас большой выбор автомобилей с пробегом. Все они проверены экспертами и технически полностью исправны.",
                    "money" => "Оплата договорная",
                    "images" => array(
                        "lg" => "/resources/img/service/inspection.png",
                        "md" => "/resources/img/service/inspection-d.png",
                    ),
                );
            break;
            case 'servicnye':
                $data = array(
                    "h1" => "Сервисные услуги",
                    "form" => "по сервисным услугам",
                    "ul" => array(
                        "Рихтовка",
                        "Полировка",
                        "Химчистка",
                        "Чистка фар",
                        "Полировка стекол",
                        "Малярные работы",
                        "Мелкосрочный ремонт",
                        "Удаление вмятин без покраски и нарушения ЛКП",
                        "Перешив салона в кожу или алькантару <br /> и отдельных элементов (руль, кресло, подлокотник)",
                    ),
                    "money" => "Оплата договорная",
                    "images" => array(
                        "lg" => "/resources/img/service/servicnye.png",
                        "md" => "/resources/img/service/servicnye-d.png",
                    ),
                );
            break;
        }
        $response = array(
            "data" => $data,
        );
        return view("/client/service/item", $response);
    }
}
