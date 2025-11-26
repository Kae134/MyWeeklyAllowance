Login :

  Global :
     - shouldLoginAsParentAndReturnCorrectData : verify that the user can login with valid credentials and have the proper permissions.
     - shouldLoginAsChildAndReturnCorrectData : verify that the user can login with valid credentials and have the proper permissions.  
 
  Email :
     - shouldThrowExceptionWhenEmailIsMissing : verify that an exception is thrown when the email is not entered   
     - shouldThrowExceptionWhenEmailIsNotInDb : verify that an exception is thrown when the email is not in the db     
     - shouldThrowExceptionWhenEmailIsInvalid : verify that an exception is thrown when the email is not well formated
  
  Password :
     - shouldThrowExceptionWhenPasswordIsNotMissing : verify that an exception is thrown when the password is not entered
     - shouldThrowExceptionWhenPasswordDoesNotMatchUserEmail : verify that an exception is thrown when the password doesnt match the one of the email
     - shouldThrowExceptionWhenPasswordIsInvalid : verify that an exception is thrown when the password is not well formated
    
  
SignIn :

 Global :
     - shouldSigninAndReturnParentDataWithValidCredentials : verify that the user when with parent permissions can login with valid credentials and have the proper data.
     - shouldSigninAndReturnChildDataWithValidCredentials : verify that the user when with child permissions can login with valid credentials and have the proper data.  
 
  Email :
     - shouldThrowExceptionWhenEmailIsMissing : verify that an exception is thrown when the email is not entered
     - shouldThrowExceptionWhenEmailIsInvalid : verify that an exception is thrown when the email is not well formated
  
  Password :
     - shouldThrowExceptionWhenPasswordMissing : verify that an exception is thrown when the password is not entered
     - shouldThrowExceptionWhenPasswordIsInvalid : verify that an exception is thrown when the password is not well formated
  
  Name :
     - shouldThrowExceptionWhenNameIsMissing : verify that an exception is thrown when the Name is not entered
     - shouldThrowExceptionWhenNameIsInvalid : verify that an exception is thrown when the Name is not well formated
  
  Firstname :
     - shouldThrowExceptionWhenFirstnameIsMissing : verify that an exception is thrown when the Firstname is not entered
     - shouldThrowExceptionWhenFirstnameIsInvalid : verify that an exception is thrown when the Firstname is not well formated
   

Dashboard :
  Parent :
    Create account for child :
      Global :
         - shouldCreateChildWithValidData : verify that the parent can create a child user when all the valid infos are entered.
         - shouldThrowExceptionWhenEmailIsAlreadyInUse : verify that no user is created with the same email 
        
      Email :
        - shouldThrowExceptionWhenEmailIsMissing : verify that an exception is thrown when the email is not entered
        - shouldThrowExceptionWhenEmailIsInvalid : verify that an exception is thrown when the email is not well formated
  
      Password :
        - shouldThrowExceptionWhenPasswordIsMissing : verify that an exception is thrown when the password is not entered
        - shouldThrowExceptionWhenPasswordIsInvalid : verify that an exception is thrown when the password is not well formated
  
      Name :
        - shouldThrowExceptionWhenNameIsMissing : verify that an exception is thrown when the Name is not entered
        - shouldThrowExceptionWhenNameIsInvalid : verify that an exception is thrown when the Name is not well formated
  
      Firstname :
        - shouldThrowExceptionWhenFirstnameIsMissing : verify that an exception is thrown when the Firstname is not entered
        - shouldThrowExceptionWhenFirstnameIsInvalid : verify that an exception is thrown when the Firstname is not well formated
  
    Deposit Money on child account :
      Global :
        - shouldAddMoneyToChildAccount : verify that money has been added to child
        - shouldRemoveMoneyFromParent : verify that money has been removed from parent
        - shouldThrowExceptionWhenNotEnoughMoney : verify that an exception is thrown when there is not enought money on the parent account for the transfer
    
      Amount :
        - shouldThrowExceptionWhenAmountIsMissing : verify that an exception is thrown when the amount is not entered
        - shouldThrowExceptionWhenAmountIsInvalid : verify that an exception is thrown when the amount is not well formated
     
      Child Email :
        - shouldThrowExceptionWhenChildEmailNotInDb : verify that an exception is thrown when the child doesnt exist
        - shouldThrowExceptionWhenChildEmailIsMissing : verify that an exception is thrown when the email is not entered
        - shouldThrowExceptionWhenChildEmailIsInvalid : verify that an exception is thrown when the email is not well formated
      
      Password :
        - shouldThrowExceptionWhenPasswordDoesNotMatchParent : verify that an exception is thrown when the password is not the one of the parent
        - shouldThrowExceptionWhenPasswordIsMissing : verify that an exception is thrown when the password is not entered
        - shouldThrowExceptionWhenPasswordIsInvalid : verify that an exception is thrown when the password is not well formated
      
    Save The Expense :
      Global :
        - shouldSaveChildExpense : verify that the child expense has been saved permanently
      
      Expense Id :
        - shouldThrowExceptionWhenExpenseIdIsMissing : verify that an exception is thrown when the Expense id is missing
        - shouldThrowExceptionWhenExpenseIdNotInDb : verify that an exception is thrown when the Expense is in the db
     
    Fix Monthly Allowance :
      Global :
        - shouldFixAllowanceToChild : verify that an allowance has been added to the child
          
      Amount :
        - shouldThrowExceptionWhenAmountIsMissing : verify that an exception is thrown when the amount is not entered
        - shouldThrowExceptionWhenAmountIsInvalid : verify that an exception is thrown when the amount is not well formated
     
    Add Money To Account :
      Global :
        - shouldAddMoneyToParent : verify that money has been added to the user
      
      Amount :
        - shouldThrowExceptionWhenAmountIsMissing : verify that an exception is thrown when the amount is not entered
        - shouldThrowExceptionWhenAmountIsInvalid : verify that an exception is thrown when the amount is not well formated
     
  Child :
    Spend money :
      Global :
        - shouldRemoveMoneyFromChild : verify that money has been removed from the user
        - shouldThrowExceptionWhenNotEnoughMoney : verify that an exception is thrown when there is not enought money on the child account
      
      Amount :
        - shouldThrowExceptionWhenAmountIsMissing : verify that an exception is thrown when the amount is not entered
        - shouldThrowExceptionWhenAmountIsInvalid : verify that an exception is thrown when the amount is not well formated
    
      Password :
        - shouldThrowExceptionWhenPasswordDoesNotMatchParent : verify that an exception is thrown when the password is not the one of the parent
        - shouldThrowExceptionWhenPasswordIsMissing : verify that an exception is thrown when the password is not entered
        - shouldThrowExceptionWhenPasswordIsInvalid : verify that an exception is thrown when the password is not well formated
