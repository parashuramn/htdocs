const options = {
 // url:'http://xapi.vocab.pub/sparql?query=++++PREFIX+xapi%3A+%3Chttps%3A%2F%2Fw3id.org%2Fxapi%2Fontology%23%3E++++SELECT+DISTINCT+%2A++++WHERE++++%7B++++++%7B++++++++SELECT+DISTINCT+%3FVerb++++++++WHERE++++++++%7B++++++++++%3FVerb+a+xapi%3AVerb++++++++%7D++++++%7D++++++%3FVerb+skos%3AprefLabel+%3FTitle+.++++++%7D++++ORDER+BY+%3FTitle++&format=application/json&default-graph-uri=&timeout=0&debug=on',
  url: "https://www.nextsoftwaresolutions.com/zapier/verbs.json",
  method: 'GET',
  headers: {
    'Accept': 'application/json'
  },
  params: {

  }
}
Object.flatten = function(data) {
    var result = {};
    function recurse (cur, prop) {
        if (Object(cur) !== cur) {
            result[prop] = cur;
        } else if (Array.isArray(cur)) {
             for(var i=0, l=cur.length; i<l; i++)
                 recurse(cur[i], prop + "[" + i + "]");
            if (l == 0)
                result[prop] = [];
        } else {
            var isEmpty = true;
            for (var p in cur) {
                isEmpty = false;
                recurse(cur[p], prop ? prop+"."+p : p);
            }
            if (isEmpty && prop)
                result[prop] = {};
        }
    }
    recurse(data, "");
    return result;
};
var output_fields =[{
'Title.type': "verb_title_type",
'Title.value': "verb_title_display",
'Title.xml:lang': "verb_display_lang",
'Verb.type': "verb_type",
'Verb.value': "verb_value",
'id': "id"
}];
var output_field_keys = Object.keys(output_fields[0]);
return z.request(options)
  .then((response) => {
    response.throwForStatus();
    const results = z.JSON.parse(response.content);
    console.log(results);
results.results.bindings.forEach(function(currentValue, res_index, res_ar) {
  // Do something with currentValue or array[index]
  //res_ar[res_index].metadata.metadata.name.actual =  res_ar[res_index].metadata.metadata.name['en-US'];
res_ar[res_index].id =  res_ar[res_index].Verb.value;
});
 //return results.results.bindings; 
     //valid fields
     var return_fields = [];
     for(var flds_ar in results.results.bindings){
         var flatten_results =Object.flatten(results.results.bindings[flds_ar]);
         var return_fields_obj ={};
     for(var flds in flatten_results){
         if(output_field_keys.indexOf(flds) !== -1)
          return_fields_obj[output_fields[0][flds]] =flatten_results[flds];
     
     }
     return_fields.push(return_fields_obj);
 }
     return return_fields;
    // You can do any parsing you need for results here before returning them
  });
  //function ttp(item){return item.metadata.metadata.name.actual = item.metadata.metadata.kind;}