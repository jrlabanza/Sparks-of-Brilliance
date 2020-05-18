$(document).ready(function() { //table
    $('table.download').DataTable({
		"ordering": false,
		paging: false,
        dom: 'Bfrtip',
        buttons: 
        [
            { extend: 'excel',title: '', text: 'Download as Excel', className: 'mb-1',filename: function(){
                var d = new Date();
                return 'SOB' + d;
            }},
            {
                extend: 'pdf',title: '', text: 'Download as PDF', className: 'mb-1',filename: function(){
                    var d = new Date();
                    return 'SOB' + d;
                }  
            }
        ]
	});
    
    $('table.display').DataTable({
		"ordering": false,
		});
    
    $('table.hr').DataTable({
        "ordering": false,
		paging: false,
        dom: 'Bfrtip',
        buttons: [{ extend: 'excel', text: 'Download as Excel', className: 'mb-1',filename: function(){
                var d = new Date();
                return 'SOB' + d;
        }}]
	});
    
    $('.example').DataTable();
    $("div.other").hide();
    $("div.amount_modifier").hide();
    $("input#default_amount").prop("checked",true);
    $("input.custom_amount").hide();
    $("tr.edit-master").hide();
});


$(".empName").on("change", function(){ //auto fill associate

    var selectedEmp = $(this).val();
    var empID = $("#listall option[value='"+ selectedEmp +"'").attr("data-empID");
    var rowCIDid = $(this).attr("data-add-cid");

    $.post(

        "functions/getEmployeeData.php",
        {

          "empID" :   empID
          
        },function(data){
            console.log(data);
            var obj = JSON.parse(data);
           
            $("input.cid#"+ rowCIDid).val(obj.cidNum);

        }
    );
});



$(document).on("change", ".key_strat", function(){ //others show/hide

    var otherID = $(this).attr("data-other-key-strgy");

    if ($(this).val() == "Others") {

        $("div.other#"+otherID).show();
        
    }
    else{
        $("div.other#"+otherID).hide();
        $("input.others-specify#"+otherID).val("");
    }
});


$(document).on("input", ".no_of_associates", function(){ //no of associates condition

    if ($(this).val() > "1") {

        $("div.amount_modifier").show();
        
    }

    else {

        $("div.amount_modifier").hide()
        $("input#default_amount").prop("checked",true);
        $("input.custom_amount").hide();
        $("input.custom_amount").val("");
    }
    

});

$(document).on("change", "input.amount", function(){ //input amount condition

    if ($(this).val() == "eq"){
        $("input.custom_amount").show();
        $("input.custom_amount").val("");
    }
    else if ($(this).val() == "da"){
        $("input.custom_amount").show();
        $("input.custom_amount").val("");
    }
    else if ($(this).val() == "mi"){
        $("input.custom_amount").hide();
    }
});


$(document).on('click', 'a.edit-button', function(){

    e.preventDefault();
    alert('test');
    
    // //get data
    // var machineID = $(this).attr("data-machine-id");

});

$("#master_cid").on("keyup", function(){
var cid = $(this).val();
if($(this).val().length == 8){
    $.post(
        "functions/getDatafromCID.php",
        {
            "empID": cid    
        },
        function(data){
            var obj = JSON.parse(data);
            $("#master_lastName").val(obj.lastName);
            $("#master_firstName").val(obj.firstName);
            $("#master_email").val(obj.email);
            if(obj == "")
            {
                alert("CID NUMBER NOT EXISITNG IN THE SOB DATABASE, PLEASE REGISTER CID AT USERBASE LIST");
            }
        }
    )
}
});


