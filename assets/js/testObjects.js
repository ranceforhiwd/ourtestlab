/**
 * Test Objects
 * @author Rance Aaron
 */
console.log('Test Objects');

/************************Container Objects **********************/

function MyObject(one, two, three) {
  this.one = one;
  this.two = two;
  this.three = three;
  this.component = Object.create(Component);
  this.action = function(){
      console.log('action:', this.two);
  };
}

/************************Component Objects *********************/

var Component = {
  property1: 'property 1',
  displayType: function() {  
    console.log(this.property1);
  }
};

/***********************Testbench ******************************/

testObj = new MyObject('first', 'second', 'third');
testObj.component.displayType();
testObj.action();