<div class="bg-secondary text-white">
    <span>Canvas left => @{{canvas? mouse.canvasLeft : 0}}</span>
    <span>Canvas top =>  @{{canvas? mouse.canvasTop : 0}}</span>
    <br>
    <span>Canvas Width =>  @{{canvas? canvas.width : 0}}</span>
    <span>Canvas Height => @{{canvas? canvas.height : 0}}</span>
</div>

<div class="bg-info">
    <span>Mouse Client X => @{{mouse.clientX}}</span>
    <span>Mouse Client Y => @{{mouse.clientY}}</span>
    <br>
    <span>Mouse X => @{{mouse.x}}</span>
    <span>Mouse Y => @{{mouse.y}}</span>
</div>

<div class="bg-secondary text-white">
    <span>Positions movement movementX => @{{positions? positions.movementX : 0}}</span>
    <span>Positions movement movementY => @{{positions? positions.movementY : 0}}</span>
    <br>
    <span>Positions movement clientX => @{{positions? positions.clientX : 0}}</span>
    <span>Positions movement clientY => @{{positions? positions.clientY : 0}}</span>
</div>