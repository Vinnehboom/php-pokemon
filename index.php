<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stylesheet.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=VT323&display=swap"
      rel="stylesheet"
          />
    <title>Pokedex</title>
  </head>
  <body>

    <main class="container">
      <img class="pokedex-logo" src="/img/pokedexlogo.png" alt="Pokedex logo"/>
        <div class="grid-container interface">
            <div class="search-container">
                <form action="index.php" method="get">
                    <div class="search-items-grouped">
                        <input  autocomplete="false"  name="pokemon"  type="text" placeholder="Who's that Pokemon?" class="search bar" id="pokemon-search"/>
                            <button class="search-button hvr-buzz" id="submit" >
                            <img class="pokeball hvr-buzz" src="https://cdn.emojidex.com/emoji/seal/pokeball.png" emoji-code="pokeball" alt="pokeball"/>
                            </button>
                             <label class="tickbox" for="shiny">Shiny</label>
                             <input id="shiny-check" name="shiny" type="checkbox">
                            <span class="checkmark"></span>

                    </div>
                </form>
   <?php
   $api = "https://pokeapi.co/api/v2/";

   if(isset($_GET["pokemon"])) {
       $pokemonName = strtolower(implode('-', explode(' ',($_GET['pokemon']))));
       $data =  file_get_contents ( $api . "pokemon/" . $pokemonName  ) ;
       $response = json_decode ( $data, true);
   }
   ?>
                <div class="button toggle-previous button-3" id="previous-button">
              <div id="circle"></div>
              <a href="#">Previous</a>
            </div>
            <div class="button toggle-next button-3" id="next-button">
              <div id="circle"></div>
              <a href="#">Next</a>
            </div>
          <div class="button evolve button-3" id="evolve-button">
            <div id="circle"></div>
            <a href="#">Evolve!</a>
          </div>
          <div class="button species button-3" id="species-button">
            <div id="circle"></div>
            <a href="#">Species</a>
          </div>
          </div>


          <div class="pokemon-images" >
            <div class="pokemon-on-display">
                <?php

                if(isset($_GET['shiny'])) {
                    echo "<div id='main-sprites'><img src='" . $response["sprites"]["front_shiny"] . "'>";
                    echo "<img src='" . $response["sprites"]["back_shiny"] . "'></div>";
                } else {
                    echo "<div id='main-sprites'><img src='" . $response["sprites"]["front_default"] . "'>";
                    echo "<img src='" . $response["sprites"]["back_default"] . "'></div>";
                }

                if (isset($_GET['pokemon'])) {
                    $data2 = file_get_contents($api . "pokemon-species/" . $pokemonName);
                    $response2 = json_decode($data2, true);
                    if (isset($_GET['pokemon'])) {

                        $dataEvo = file_get_contents($response2['evolution_chain']['url']);
                        $responseEvo = json_decode($dataEvo, true);
                        // function to fetch all sprites & if so shinies
                        function getSprites($array)
                        {
                            $evolutionSprites = [];
                            foreach ($array as $pokemon) {
                                $baseURL = 'https://pokeapi.co/api/v2/pokemon/';
                                $fetchURL = $baseURL . $pokemon;
                                $data = file_get_contents($fetchURL);
                                $response = json_decode($data, true);
                                if (isset($_GET['shiny'])) {
                                    $spriteURL = $response['sprites']['front_shiny'];
                                } else {
                                    $spriteURL = $response['sprites']['front_default'];
                                }
                                array_push($evolutionSprites, $spriteURL);
                            }
                            echo "<div id='evolutions'>";
                            foreach ($evolutionSprites as $url) {
                                echo "<img src='" . $url . "' alt=''/>";
                            }
                            echo "</div>";

                        }

                        $chainArray = $responseEvo['chain'];
                        $evolutionLineArray = [];
                        $evolutionLength = count($chainArray['evolves_to']);
                        if($evolutionLength === 0) {
                            $evolutionLineArray = [];
                        } else if ($evolutionLength > 1) {
                            foreach ($chainArray['evolves_to'] as $evolution) {
                                array_push($evolutionLineArray, $evolution['species']['name']);
                            }
                        } else {
                            $pokemon1 = ''; $pokemon2 = ''; $pokemon3 ='';
                            if($pokemonName === $chainArray['species']['name']) {
                                array_push($evolutionLineArray, $pokemonName);
                                while ($chainArray['evolves_to']) {
                                    $evolutionLength = count($chainArray['evolves_to']);
                                    if ($evolutionLength > 1) {
                                        foreach ($chainArray['evolves_to'] as $evolution) {
                                            array_push($evolutionLineArray, $evolution['species']['name']);
                                        }
                                        $chainArray = $chainArray['evolves_to'][0];
                                    } else if($evolutionLength === 1) {

                                    array_push($evolutionLineArray, $chainArray['evolves_to'][0]['species']['name']);
                                    $chainArray = $chainArray['evolves_to'][0];
                                    }
                                }
                            } else if($pokemonName === $chainArray['evolves_to'][0]['species']['name']) {
                                $pokemon2 = $pokemonName;
                                $pokemon1 = $chainArray['species']['name'];
                                array_push($evolutionLineArray, $pokemon1, $pokemon2);
                                if ($chainArray['evolves_to'][0]['evolves_to'][0]) {
                                    $pokemon3 = $chainArray['evolves_to'][0]['evolves_to'][0]['species']['name'];
                                    array_push($evolutionLineArray, $pokemon3);
                                }

                            } else {
                                $pokemon3 = $pokemonName;
                                $pokemon2 = $chainArray['evolves_to'][0]['species']['name'];
                                $pokemon1 = $chainArray['species']['name'];
                                array_push($evolutionLineArray, $pokemon1,$pokemon2,$pokemon3);
                            }

                        }
                        getSprites($evolutionLineArray);
                    };
                };
                ?>
            </div>
          </div>
          <div class="pokemon-data">
            <div class="data-points">
                <?php
                if(isset($_GET['pokemon'])) {
                    $name = ucfirst($pokemonName);

                $id = $response["id"];
                echo "<div id='id-display'>" . "#" . $id . "</div>";
                echo "<div id='name-target'>" . $name . "</div>";

                $typeArray = [];
                $types = $response['types'];
                $typeLength = count($types);
                for ($j = 0; $j < $typeLength; $j++) {
                    array_push($typeArray, $types[$j]['type']['name']);
                }
                echo "<div>";
                foreach ($typeArray as $type) {
                    echo strtoupper($type) . " ";
                }
                echo "</div>";

                $data3 =  file_get_contents ( $api . "pokemon-species/" . $pokemonName);
                $response3 = json_decode($data3,true);
                $allGenera = $response3['genera'];
                $englishGenus = '';
                    foreach ($allGenera as $genus) {
                        if ($genus['language']['name'] === 'en'){
                            $englishGenus = $genus['genus'];
                        }
                    }
                    echo "<div id='genus-target'> The " . $englishGenus . "</div><br/>";
                $allFlavor = $response3['flavor_text_entries'];
                $englishFlavors = [];
                foreach ($allFlavor as $flavor) {
                    if ($flavor['language']['name'] === "en") {
                        array_push($englishFlavors, $flavor['flavor_text']);
                    }
                }
                $randomFlavor = $englishFlavors[mt_rand(0, count($englishFlavors)-1)];
                echo "<div id='flavor'>" . $randomFlavor . "</div><br/>";


                $allMoves = $response['moves'];
                $totalMoveLength = count($allMoves);
                $movesAmount = min(4, $totalMoveLength);
                echo "<div>Available moves:<ul id='moves-list'>";
                for($i = 0; $i < $movesAmount; $i++) {
                    $moveToAdd = $allMoves[mt_rand(0, $totalMoveLength-1)]['move']['name'];
                    $moveToAdd = implode(" ",explode("-",$moveToAdd));
                    echo "<li>" . ucwords($moveToAdd). "</li>";
                }
                echo "</ul></div>";

                }
                ?>
            </div>
          </div>
        </div>
      </div>
    </main>
    <script src="script.js"></script>
  </body>
</html>




