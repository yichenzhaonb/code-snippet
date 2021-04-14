function unique(array) {
    return array.reduce(function(previous,number){
         let result = previous.find(function(a){
              return a==number;
            });
      //console.log(uniqueNumber);
          if(result===undefined){
               previous.push(number);
          }
     
        return previous;
     },[]);
   
 }

 console.log(unique([1,2,3,4,4,5]));