<?php
// Nastavení hlavičky pro UTF-8
header('Content-Type: text/html; charset=utf-8');

// Prodloužené texty o slovanských nadpřirozených bytostech
$strašidelné_texty = array(
    "Kostěj" => "V temném lese, kde slunce nikdy neprosvítí, se skrývá Kostěj, nemrtvý čaroděj s kostnatými prsty a zářícími očima, které svítí v temnotě jako smrtící hvězdy. Jeho zlověstný smích, jakoby z hlubin pekla, se nese nocí jako varovný signál. Unáší zbloudilé duše do svého hradu, kde kosti a lebky vytvářejí děsivou krajinu, která děsí i ty nejstatečnější. Jeho touha po nesmrtelnosti nezná hranic a každou noc plánuje, jak přivést další oběti do svého temného království.",
    "Baba Jaga" => "Baba Jaga, stará čarodějnice s dlouhými, zplihlými vlasy a zubami jako drápy, žije v pohyblivé chýši, která stojí na kuřích nožkách a klopýtá po lesích a loukách, jakoby chtěla uniknout před světem. Její chýše se neustále pohybuje, aby lépe chytala své oběti, a její zlý smích je slyšet po celém lese. Chytá zbloudilé děti a přetváří je v kamenné sochy, které jsou jejími věčnými vězni, a nikdy je nemůže pustit zpět do světa živých.",
    "Hejkal" => "Když se v lese setmí a měsíc začne zářit, zpoza starých, zakroucených stromů zazní děsivý křik Hejkala, strašidelného tvora s dlouhými drápy a vlasy z mechu, které visí jako smrtelné liany. Tato bytost se skrývá ve stínu, jehož temnota je hlubší než jakákoli noc, a pokud někdo narazí na jeho území, ztratí se v jeho temné říši, která je plná iluzí a klamů. Hejkalova hrozba se rozprostírá na celé okolí, a každý, kdo se dostane příliš blízko, čelí strašlivému osudu.",
    "Polednice" => "Polední slunce vylévá své žhavé paprsky na opuštěné pole, kde se zjeví Polednice, temná postava zahalená v plátně, která se zjevuje jako smrtelný předzvěst v době poledne. Hrozivě se blíží k dětem, které se odváží hrát venku, a její přítomnost je jako mrazivý dotyk smrti. S každým krokem, který udělá, se blíží k těm, kteří si dovolí zůstat venku po poledni, a strhne je do svého světa temné magie, kde už nikdy nespatří světlo a jejich duše budou uvězněny navždy.",
    "Vodník" => "Ve vodách temného a zakaleného rybníka, kde žádné světlo nedosáhne, čeká Vodník, zelený mužík s blánami mezi prsty a pokrouceným úsměvem, který se třpytí jako skleněné střepy. Chytá neopatrné plavce do svých smrtících sítí a jejich duše uvězní ve sklenicích na dně tůně, které jsou jako věčné hroby. Když se k vodě přiblížíte, jeho žabí oči, které vyzařují chladný, pronikavý pohled, sledují každý váš krok a čekají na svou příležitost.",
    "Rusalka" => "Ve stříbřitém měsíčním svitu zpívá Rusalka na břehu jezera, její hlas zní jako jemný, ale smrtelně nebezpečný šepot. Její píseň je úchvatná a svůdná, ale zároveň naplněná smrtelnou pastí, která láká námořníky a zbloudilé poutníky, kteří se kvůli její kráse a hypnotickému zpěvu vrhají do hluboké vody. Tam, v chladných hlubinách jezera, se utopí a stanou se jejími poddanými, jejich duše navždy ztraceny v jejím království pod hladinou.",
);

$texty = array("Kostěj" => "jsme obkliceni",
               "Baba Jaga" => "nemeckou jednotkou",
               "Hejkal" => "potrebujeme posily",
                "Polednice" => "uz nemama moc casu",
                "Vodník" => "pokud to nestihnete",
                "Rusalka" => "budeme bojovat az do konce"
    );

// Rozměry tabulky
$rows = 22;
$cols = 22;
$target_length = $rows * $cols - 5; // Cílová délka textu (asi 620 znaků)

// Generování HTML dokumentu
echo "<!DOCTYPE html>";
echo "<html lang='cs'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Tabulky s texty</title>";
echo "<style>";
echo "table { border-collapse: collapse; margin-bottom: 20px; }";
echo "td { width: 20px; height: 20px; text-align: center; vertical-align: middle; font-family: monospace; }";
echo "th { background: #f0f0f0; }";
echo ".header { text-align: center; font-weight: bold; }";
echo "</style>";
echo "</head>";
echo "<body>";

foreach ($strašidelné_texty as $název => $text) {
    // Zkrácení nebo prodloužení textu na požadovanou délku
    if (mb_strlen($text, 'UTF-8') > $target_length) {
        $text_combined = mb_substr($text, 0, $target_length, 'UTF-8');
    } else {
        $text_combined = str_pad($text, $target_length, " ");
    }

    // Generování tabulky
    echo "<h2>$název</h2>";
    echo "<table border='1'>";

    // Vytvoření horního řádku se souřadnicemi osy X
    echo "<tr><th class='header'></th>";
    for ($col = 0; $col < $cols; $col++) {
        echo "<th class='header'>$col</th>";
    }
    echo "</tr>";

    for ($row = 0; $row < $rows; $row++) {
        echo "<tr>";
        // Levý sloupec se souřadnicemi osy Y
        echo "<th class='header'>$row</th>";
        for ($col = 0; $col < $cols; $col++) {
            $pos = $row * $cols + $col;
            $char = htmlspecialchars(mb_substr($text_combined, $pos, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            echo "<td>$char</td>";
        }
        echo "</tr>";
    }

    echo "</table>";
    echo $texty[$název].'<br>';
}

echo "</body>";
echo "</html>";
?>
