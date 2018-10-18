/*
	Tests ported from Pointer.js
	------------------------------------
	http://smus.com/mouse-touch-pointer/
*/

var PointerTypes = {
	TOUCH: 'touch',
	MOUSE: 'mouse'
};

window.addEventListener('load', function() {
  example = document.querySelector('#test');
});

function synthesizeEvent(eventName, extras) {
  var event = document.createEvent('CustomEvent');
  event.initEvent(eventName, true, true);
  for (var k in extras) {
    event[k] = extras[k];
  }
  example.dispatchEvent(event);
}

var mockGetPointerList = {
	pointers: [{
		x: 30,
		y: 50,
		type: PointerTypes.TOUCH,
		identifier: 3
	}],
	pageX: 30, 
	pageY: 50
};

module('pointer');

/***** touch *****/
if (Modernizr.touch) {
  test('touchstart should cause a pointerdown event', function() {
    example.addEventListener('pointerdown', function(e) {
      start();
      ok(true, 'pointerdown fired!');
      equal(e.pointerType, PointerTypes.TOUCH, 'pointer is a touch');
      example.removeEventListener('pointerdown', arguments.callee);
    });
    stop();
    synthesizeEvent('touchstart', {targetTouches: [{pageX: 100, pageY: 200}]});
  });


  test('touchmove should cause pointermove', function() {
    example.addEventListener('pointermove', function(e) {
      start();
      ok(true, 'pointermove fired!');
      equal(e.pointerType, PointerTypes.TOUCH, 'pointer is a touch');
      example.removeEventListener('pointermove', arguments.callee);
    });
    stop();
    synthesizeEvent('touchmove', {targetTouches: [{pageX: 100, pageY: 200}]});
  });

  test('touchend should cause pointerup', function() {
    example.addEventListener('pointerup', function(e) {
      start();
      ok(true, 'pointerupfired!');
      equal(e.pointerType, PointerTypes.TOUCH, 'pointer is a touch');
      example.removeEventListener('pointerup', arguments.callee);
    });
    stop();
    synthesizeEvent('touchend', {targetTouches: [{pageX: 100, pageY: 200}]});
  });

  test('multi-touch events should create multi-pointer events at proper coords', function() {
    example.addEventListener('pointermove', function(e) {
      start();
      var pointers = e.pointers;
      equal(pointers.length, 2, 'two pointers');
      var second = pointers[1];
      equal(second.x, 300, 'second x corresponds');
      equal(second.y, 100, 'second y corresponds');
      example.removeEventListener('pointermove', arguments.callee);
    });
    stop();
    synthesizeEvent('touchmove', {targetTouches:
                    [{pageX: 100, pageY: 200}, {pageX: 300, pageY: 100}]});
  });
} else {
  console.log('Note: skipping touch* tests');
}


/**** mouse ****/

if (!Modernizr.touch) {
  test('mousedown should cause a pointerdown event', function() {
    // Expect a pointerdown event.
    example.addEventListener('pointerdown', function(e) {
      start();
      ok(true, 'pointerdown fired!');
      // Check that the pointerType is as expected.
      equal(e.pointerType, PointerTypes.MOUSE, 'pointer is a mouse');
      example.removeEventListener('pointerdown', arguments.callee);
    });
    // Synthesize a mousedown event.
    stop();
    synthesizeEvent('mousedown', {pageX: 100, pageY: 200});
  });

  test('mousemove should cause pointermove', function() {
    example.addEventListener('pointermove', function(e) {
      start();
      ok(true, 'pointermove fired!');
      equal(e.pointerType, PointerTypes.MOUSE, 'pointer is a mouse');
      example.removeEventListener('pointermove', arguments.callee);
    });
    stop();
    synthesizeEvent('mousemove', {pageX: 300, pageY: 200});
  });

  test('mouseup should cause pointerup', function() {
    example.addEventListener('pointerup', function(e) {
      start();
      ok(true, 'pointerup fired!');
      equal(e.pointerType, PointerTypes.MOUSE, 'pointer is a mouse');
      example.removeEventListener('pointerup', arguments.callee);
    });
    stop();
    synthesizeEvent('mouseup', {pageX: 100, pageY: 50});
  });

  test('mousedown position should be passed to pointer event', function() {
    example.addEventListener('pointerdown', function(e) {
      start();
      var pointers = e.pointers;
      equal(pointers.length, 1, 'expecting one pointer');
      var point = pointers[0];
      equal(point.x, 300, 'x coordinate is right!');
      equal(point.y, 200, 'y coordinate is right!');
      synthesizeEvent('mouseup', {pageX: 300, pageY: 200});
      example.removeEventListener('pointerdown', arguments.callee);
    });
    stop();
    synthesizeEvent('mousedown', {pageX: 300, pageY: 200});
  });

  test('getPointerList should only show mouse if down', function() {
    example.addEventListener('pointermove', function(e) {
      start();
      var pointers = e.pointers;
      equal(pointers.length, 0, 'expecting no pointer');
      example.removeEventListener('pointermove', arguments.callee);
    });
    stop();
    synthesizeEvent('mousemove', {pageX: 300, pageY: 200});
  });

} else {
  console.log('Note: skipping mouse* tests');
}

if (window.navigator.msPointerEnabled) {
  /**** MSPointer ****/
  test('MSPointerDown should cause a pointerdown event', function() {
    ok(false, 'implement me!');
  });

  test('MSPointerMove should cause pointermove', function() {
    ok(false, 'implement me!');
  });

  test('MSPointerUp should cause pointerup', function() {
    ok(false, 'implement me!');
  });
} else {
  console.log('Note: skipping MSPointer* tests');
}

/****** GESTURAL STUFF ******/

module('gesture');

test('doubletap should work based on pointer events', function() {
  expect(1);
  example.addEventListener('dbltap', function(e) {
    start();
    ok(true, 'doubletap fired!');
    example.removeEventListener('dbltap', arguments.callee);
  });
  synthesizeEvent('mousedown', mockGetPointerList);
  synthesizeEvent('mouseup', mockGetPointerList);
  setTimeout(function() {
    synthesizeEvent('mousedown', mockGetPointerList);
	synthesizeEvent('mouseup', mockGetPointerList);
  }, 200);
  stop();
});

test('doubletap should not fire if the delay between pointerdowns is too large', function() {
  var didFire = false;
  expect(1);
  example.addEventListener('dbltap', function(e) {
    didFire = true;
    example.removeEventListener('dbltap', arguments.callee);
  });
  synthesizeEvent('mousedown', mockGetPointerList);
  synthesizeEvent('mouseup', mockGetPointerList);
  setTimeout(function() {
	synthesizeEvent('mousedown', mockGetPointerList);
	synthesizeEvent('mouseup', mockGetPointerList);
    setTimeout(function() {
      start();
      equal(didFire, false, 'doubletap should not fire!')
    }, 200);
  }, 1000);
  stop();
});

test('longpress should work based on pointers', function() {
  example.addEventListener('longpress', function(e) {
    start();
    ok(true, 'longpress fired!');
    example.removeEventListener('longpress', arguments.callee);
  });
  synthesizeEvent('mousedown', mockGetPointerList);
  stop();
});

test('longpress should not work if released too early', function() {
  var didLongPress = false;
  example.addEventListener('gesturelongpress', function(e) {
    didLongPress = true;
    example.removeEventListener('gesturelongpress', arguments.callee);
  });
  synthesizeEvent('mousedown', mockGetPointerList);
  setTimeout(function() {
    synthesizeEvent('mouseup', mockGetPointerList);
    setTimeout(function() {
      start();
      equal(didLongPress, false, 'longpress didnt fire: released early!');
    }, 200);
  }, 300);
  stop();
});
