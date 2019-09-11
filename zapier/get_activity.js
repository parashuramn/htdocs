const options = {
    //url: 'https://registry.tincanapi.com/api/v1/uris/activityType',
    //url: 'http://xapi.vocab.pub/sparql?default-graph-uri=&query=PREFIX+xapi%3A+%3Chttps%3A%2F%2Fw3id.org%2Fxapi%2Fontology%23%3E%0D%0Aselect+distinct+%3FActivityType+%3FTitle+%0D%0A%0D%0Awhere+%7B%0D%0A++%3FActivityType+a+xapi%3AActivityType+.%0D%0A++%3FActivityType+skos%3AprefLabel+%3FTitle+.%0D%0A+%0D%0A%0D%0A%7D%0D%0A%0D%0AORDER+BY+%3FTitle%0D%0A&format=application/json&timeout=0',
    url: 'https://www.nextsoftwaresolutions.com/zapier/activity_type.json',
    method: 'GET',
    headers: {
    },
  }
  var output_fields =[{'ActivityType.type': "activity_type",
  'ActivityType.value': "activity_value",
  'Title.type': "activity_display_type",
  'Title.value': "activity_display",
  'Title.xml:lang': "activity_language",
  'id': "id"
  }];
  var output_field_keys = Object.keys(output_fields[0]);
  return z.request(options)
    .then((response) => {
        response.throwForStatus();
        const results = z.JSON.parse(response.content);
        // You can do any parsing you need for results here before returning them
        results.results.bindings.forEach(function(currentValue, res_index, res_ar) {
          // Do something with currentValue or array[index]
          //res_ar[res_index].metadata.metadata.name.actual =  res_ar[res_index].metadata.metadata.name['en-US'];
          res_ar[res_index].id =  res_ar[res_index].ActivityType.value;
        });
        z.console.log();
        var return_fields = [];
        for(var flds_ar in results.results.bindings){
            var flatten_results =results.results.bindings[flds_ar];
            var return_fields_obj ={};
            return_fields_obj['activity_type_type']= flatten_results.ActivityType.type;
            return_fields_obj['activity_type_value']= flatten_results.ActivityType.value;
            return_fields_obj['activity_type_title_type']= flatten_results.Title.type;
            return_fields_obj['activity_type_title_lang']= flatten_results.Title['xml:lang'];
            return_fields_obj['activity_type_title_value']= flatten_results.Title.value;
        return_fields.push(return_fields_obj);
    }
        return return_fields;
    });




















// const options = {
//   //url: 'https://registry.tincanapi.com/api/v1/uris/activityType',
//   //url: 'http://xapi.vocab.pub/sparql?default-graph-uri=&query=PREFIX+xapi%3A+%3Chttps%3A%2F%2Fw3id.org%2Fxapi%2Fontology%23%3E%0D%0Aselect+distinct+%3FActivityType+%3FTitle+%0D%0A%0D%0Awhere+%7B%0D%0A++%3FActivityType+a+xapi%3AActivityType+.%0D%0A++%3FActivityType+skos%3AprefLabel+%3FTitle+.%0D%0A+%0D%0A%0D%0A%7D%0D%0A%0D%0AORDER+BY+%3FTitle%0D%0A&format=application/json&timeout=0',
//   url: 'https://www.nextsoftwaresolutions.com/zapier/activity_type.json',
//   method: 'GET',
//   headers: {
//   },
// }
// var output_fields =[{'ActivityType.type': "activity_type",
// 'ActivityType.value': "activity_value",
// 'Title.type': "activity_display_type",
// 'Title.value': "activity_display",
// 'Title.xml:lang': "activity_language",
// 'id': "id"
// }];
// var output_field_keys = Object.keys(output_fields[0]);
// return z.request(options)
//   .then((response) => {
//       response.throwForStatus();
//       const results = z.JSON.parse(response.content);
//       // You can do any parsing you need for results here before returning them
//       results.results.bindings.forEach(function(currentValue, res_index, res_ar) {
//         // Do something with currentValue or array[index]
//         //res_ar[res_index].metadata.metadata.name.actual =  res_ar[res_index].metadata.metadata.name['en-US'];
//         res_ar[res_index].id =  res_ar[res_index].ActivityType.value;
//       });
//       z.console.log();
//       var return_fields = [];
//       for(var flds_ar in results.results.bindings){
//           var flatten_results =results.results.bindings[flds_ar];
//           var return_fields_obj ={};
//       for(var flds in flatten_results){
//           if(output_field_keys.indexOf(flds) !== -1)
//            return_fields_obj[output_fields[0][flds]] =flatten_results[flds];
      
//       }
//       return_fields.push(return_fields_obj);
//   }
//       return return_fields;
//   });