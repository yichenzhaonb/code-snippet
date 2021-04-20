const numbers = [1, 2, 3];

function double(numbers) {
    if (numbers.length ===0) {
    	return [];
    } 
	  const [x, ...y] = numbers;
    if(y.length){

  	return [x*2].concat(double(y));
    }else{
      return [x*2];
    }
       
}