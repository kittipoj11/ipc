const list = document.getElementById("list")
let output=""
async function getDataFromAPI() {
    const url = "https://jsonplaceholder.typicode.com/users/1";
    const res = await fetch(url)
    const json = await res.json()
    console.log(json)
//     json.forEach(item => {
    // })
    output += "<li>"+json.name+"</li>";
        // output += "<li><img src=" + json.url + " alt=" + json.title + "></li>";
    list.innerHTML=output
}
getDataFromAPI()