const initGame = () => {
    let randomObj = words[Math.floor(Math.random() * words.length)]; //getting random objects from words
    console.log(randomObj);
}
initGame();