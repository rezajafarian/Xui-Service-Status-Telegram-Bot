let but = document.querySelector(".but")

let servername=document.querySelector(".namesr")
let htp =document.querySelector(".slct")
let num =document.querySelector(".add")
let port =document.querySelector(".port ")
let path=document.querySelector(".path")
let user =document.querySelector(".user")
let pass =document.querySelector(".pass")
let pain=document.querySelector(".aa")
let o = true

but.addEventListener("click",()=>{ console.log("jgb"),console.log(servername.value)
    if(servername.value===""|| num.value===""||port.value===""||user.value===""||pass.value===""){ alert("اطلاعات کامل نیست")
    o=false}
     if(o===true){
        fetch('https://jsonplaceholder.typicode.com/posts',{
            method:"POST",
             body:JSON.stringify({
               servername:servername.value,
               ssl:htp.value,
               ip:num.value,
               port:port.value,
               path:path.value,
               username:user.value,
               password:pass.value
            }) ,
               headers: {"Content-Type": "application/json"} })
       .then(response=>{console.log(response.ok)
          if(response.ok){alert="sign up was a success"} }
          )
       .catch(()=>{alert="nat good"})
      } }
      
      )
   
    
     