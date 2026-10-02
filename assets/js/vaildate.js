function validateCGPA(cgpa){
    if(cgpa < 0 || cgpa > 4){
        alert("CGPA must be between 0 and 4");
        return false;
    }
    return true;
}
