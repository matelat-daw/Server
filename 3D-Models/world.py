"""
Generador de Mundo Sci-Fi Alienígena para Blender
Escena: Cañón futurista con edificios alienígenas, bosques verdes y lagos transparentes
Autor: GitHub Copilot
Fecha: Noviembre 2025
"""

import bpy
import bmesh
import math
import random
from mathutils import Vector, noise

# ============================================================================
# CONFIGURACIÓN GLOBAL
# ============================================================================

class WorldConfig:
    """Configuración del mundo"""
    CANYON_LENGTH = 500      # Longitud del cañón
    CANYON_WIDTH = 100       # Ancho del cañón
    CANYON_DEPTH = 80        # Profundidad del cañón
    
    BUILDING_COUNT = 20      # Número de edificios alienígenas
    TREE_COUNT = 150         # Número de árboles
    LAKE_COUNT = 5           # Número de lagos
    
    CREATURE_COUNT = 15      # Número de criaturas acuáticas
    
    SEED = 42                # Semilla para generación procedural

# ============================================================================
# UTILIDADES
# ============================================================================

def clear_scene():
    """Limpia toda la escena de Blender"""
    bpy.ops.object.select_all(action='SELECT')
    bpy.ops.object.delete(use_global=False)
    
    # Limpiar materiales huérfanos
    for material in bpy.data.materials:
        if not material.users:
            bpy.data.materials.remove(material)
    
    print("✓ Escena limpiada")

def create_material(name, color, metallic=0.0, roughness=0.5, emission=0.0, emission_color=None):
    """Crea un material PBR"""
    mat = bpy.data.materials.new(name=name)
    mat.use_nodes = True
    nodes = mat.node_tree.nodes
    nodes.clear()
    
    # Nodos principales
    node_principled = nodes.new(type='ShaderNodeBsdfPrincipled')
    node_output = nodes.new(type='ShaderNodeOutputMaterial')
    
    # Configurar propiedades
    node_principled.inputs['Base Color'].default_value = color
    node_principled.inputs['Metallic'].default_value = metallic
    node_principled.inputs['Roughness'].default_value = roughness
    node_principled.inputs['Emission Strength'].default_value = emission
    
    if emission_color:
        node_principled.inputs['Emission Color'].default_value = emission_color
    
    # Conectar nodos
    mat.node_tree.links.new(node_principled.outputs['BSDF'], node_output.inputs['Surface'])
    
    return mat

# ============================================================================
# GENERACIÓN DEL CAÑÓN
# ============================================================================

def create_canyon_terrain():
    """Genera el terreno del cañón con paredes rocosas"""
    print("🏔️  Generando cañón...")
    
    # Crear plano base con subdivisiones
    bpy.ops.mesh.primitive_plane_add(size=1, location=(0, 0, 0))
    terrain = bpy.context.active_object
    terrain.name = "Canyon_Terrain"
    
    # Escalar al tamaño del cañón
    terrain.scale = (WorldConfig.CANYON_LENGTH, WorldConfig.CANYON_WIDTH, 1)
    bpy.ops.object.transform_apply(scale=True)
    
    # Entrar en modo edición para subdividir
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.subdivide(number_cuts=100)
    bpy.ops.object.mode_set(mode='OBJECT')
    
    # Aplicar ruido procedural para crear el cañón
    mesh = terrain.data
    for vertex in mesh.vertices:
        x, y, z = vertex.co
        
        # Distancia desde el centro del cañón
        distance_from_center = abs(y) / (WorldConfig.CANYON_WIDTH / 2)
        
        # Crear paredes del cañón (más alto en los bordes)
        if distance_from_center > 0.3:
            height = (distance_from_center - 0.3) * WorldConfig.CANYON_DEPTH
            # Añadir ruido para irregularidad
            noise_val = noise.noise(Vector((x * 0.05, y * 0.05, 0))) * 15
            vertex.co.z = height + noise_val
        else:
            # Fondo del cañón con variaciones suaves
            vertex.co.z = noise.noise(Vector((x * 0.02, y * 0.02, 0))) * 3
    
    # Añadir modificador Subdivision para suavizar
    mod_subsurf = terrain.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 2
    mod_subsurf.render_levels = 3
    
    # Material rocoso
    mat_rock = create_material(
        "Canyon_Rock",
        color=(0.3, 0.25, 0.2, 1.0),  # Marrón rocoso
        metallic=0.1,
        roughness=0.9
    )
    terrain.data.materials.append(mat_rock)
    
    print("✓ Cañón generado")
    return terrain

# ============================================================================
# EDIFICIOS ALIENÍGENAS
# ============================================================================

def create_alien_building(location, height=30, radius=5):
    """Crea un edificio alienígena con forma orgánica"""
    
    # Base: cilindro con muchos vértices para forma orgánica
    bpy.ops.mesh.primitive_cylinder_add(
        vertices=8,
        radius=radius,
        depth=height,
        location=location
    )
    building = bpy.context.active_object
    
    # Añadir modificadores para forma alienígena
    
    # 1. SimpleDeform para torsión
    mod_twist = building.modifiers.new(name="Twist", type='SIMPLE_DEFORM')
    mod_twist.deform_method = 'TWIST'
    mod_twist.angle = random.uniform(-0.5, 0.5)
    
    # 2. Displace para textura orgánica
    mod_displace = building.modifiers.new(name="Displace", type='DISPLACE')
    
    # Crear textura para el displace
    tex = bpy.data.textures.new("BuildingTexture", type='VORONOI')
    tex.noise_scale = random.uniform(0.5, 2.0)
    mod_displace.texture = tex
    mod_displace.strength = random.uniform(0.3, 1.0)
    
    # 3. Subdivision para suavizar
    mod_subsurf = building.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 2
    
    # Material con emisión (edificios iluminados)
    emission_strength = random.uniform(1.0, 3.0)
    emission_color = (
        random.uniform(0.3, 1.0),
        random.uniform(0.3, 1.0),
        random.uniform(0.5, 1.0),
        1.0
    )
    
    mat_building = create_material(
        f"Building_{random.randint(1000, 9999)}",
        color=(0.2, 0.2, 0.25, 1.0),
        metallic=0.8,
        roughness=0.3,
        emission=emission_strength,
        emission_color=emission_color
    )
    building.data.materials.append(mat_building)
    
    return building

def generate_alien_buildings():
    """Genera todos los edificios alienígenas a lo largo del cañón"""
    print("🏛️  Generando edificios alienígenas...")
    
    random.seed(WorldConfig.SEED)
    buildings = []
    
    for i in range(WorldConfig.BUILDING_COUNT):
        # Posicionar edificios en las paredes del cañón
        x = random.uniform(-WorldConfig.CANYON_LENGTH/2 + 50, WorldConfig.CANYON_LENGTH/2 - 50)
        y = random.choice([-1, 1]) * random.uniform(20, WorldConfig.CANYON_WIDTH/2 - 10)
        z = 0
        
        height = random.uniform(20, 60)
        radius = random.uniform(3, 8)
        
        building = create_alien_building((x, y, z), height, radius)
        building.name = f"Building_{i+1}"
        buildings.append(building)
    
    print(f"✓ {len(buildings)} edificios generados")
    return buildings

# ============================================================================
# BOSQUES VERDES
# ============================================================================

def create_alien_tree(location, height=10):
    """Crea un árbol alienígena con forma única"""
    
    # Tronco
    bpy.ops.mesh.primitive_cone_add(
        vertices=6,
        radius1=0.5,
        radius2=0.8,
        depth=height * 0.4,
        location=location
    )
    trunk = bpy.context.active_object
    trunk.name = "Tree_Trunk"
    
    # Material del tronco (morado/azul alienígena)
    mat_trunk = create_material(
        "Tree_Trunk_Mat",
        color=(0.3, 0.2, 0.4, 1.0),
        roughness=0.8
    )
    trunk.data.materials.append(mat_trunk)
    
    # Copa del árbol (icosphere para forma orgánica)
    bpy.ops.mesh.primitive_ico_sphere_add(
        subdivisions=2,
        radius=height * 0.4,
        location=(location[0], location[1], location[2] + height * 0.6)
    )
    foliage = bpy.context.active_object
    foliage.name = "Tree_Foliage"
    
    # Deformar la copa
    foliage.scale = (1, 1, random.uniform(0.6, 1.2))
    bpy.ops.object.transform_apply(scale=True)
    
    # Material del follaje (verde brillante alienígena)
    mat_foliage = create_material(
        "Tree_Foliage_Mat",
        color=(0.2, 0.8, 0.3, 1.0),
        roughness=0.6,
        emission=0.2,
        emission_color=(0.2, 1.0, 0.3, 1.0)
    )
    foliage.data.materials.append(mat_foliage)
    
    # Unir tronco y copa
    bpy.ops.object.select_all(action='DESELECT')
    trunk.select_set(True)
    foliage.select_set(True)
    bpy.context.view_layer.objects.active = trunk
    bpy.ops.object.join()
    
    return trunk

def generate_forest():
    """Genera bosques a lo largo del cañón"""
    print("🌳 Generando bosques...")
    
    random.seed(WorldConfig.SEED + 100)
    trees = []
    
    for i in range(WorldConfig.TREE_COUNT):
        # Posicionar árboles en el fondo del cañón
        x = random.uniform(-WorldConfig.CANYON_LENGTH/2 + 30, WorldConfig.CANYON_LENGTH/2 - 30)
        y = random.uniform(-15, 15)  # Centro del cañón
        z = 0
        
        height = random.uniform(8, 15)
        
        tree = create_alien_tree((x, y, z), height)
        tree.name = f"Tree_{i+1}"
        trees.append(tree)
    
    print(f"✓ {len(trees)} árboles generados")
    return trees

# ============================================================================
# LAGOS CON AGUA TRANSPARENTE
# ============================================================================

def create_lake(location, size=20):
    """Crea un lago con agua transparente y fondo visible"""
    
    # Superficie del agua
    bpy.ops.mesh.primitive_plane_add(size=size, location=location)
    lake_surface = bpy.context.active_object
    lake_surface.name = "Lake_Surface"
    
    # Subdividir para ondulaciones
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.subdivide(number_cuts=20)
    bpy.ops.object.mode_set(mode='OBJECT')
    
    # Añadir ondulaciones sutiles
    mesh = lake_surface.data
    for vertex in mesh.vertices:
        x, y, z = vertex.co
        wave = noise.noise(Vector((x * 0.3, y * 0.3, 0))) * 0.1
        vertex.co.z = wave
    
    # Material de agua transparente
    mat_water = bpy.data.materials.new(name="Water_Material")
    mat_water.use_nodes = True
    nodes = mat_water.node_tree.nodes
    links = mat_water.node_tree.links
    nodes.clear()
    
    # Configurar shader de agua
    node_principled = nodes.new(type='ShaderNodeBsdfPrincipled')
    node_output = nodes.new(type='ShaderNodeOutputMaterial')
    
    # Color azul-verde alienígena transparente
    node_principled.inputs['Base Color'].default_value = (0.1, 0.6, 0.8, 1.0)
    node_principled.inputs['Metallic'].default_value = 0.0
    node_principled.inputs['Roughness'].default_value = 0.1
    node_principled.inputs['Transmission'].default_value = 0.95  # Transparencia
    node_principled.inputs['IOR'].default_value = 1.33  # Índice de refracción del agua
    
    links.new(node_principled.outputs['BSDF'], node_output.inputs['Surface'])
    
    lake_surface.data.materials.append(mat_water)
    
    # Fondo del lago
    bpy.ops.mesh.primitive_plane_add(size=size, location=(location[0], location[1], location[2] - 3))
    lake_bottom = bpy.context.active_object
    lake_bottom.name = "Lake_Bottom"
    
    # Material del fondo (arena/roca)
    mat_bottom = create_material(
        "Lake_Bottom_Mat",
        color=(0.6, 0.5, 0.3, 1.0),
        roughness=0.9
    )
    lake_bottom.data.materials.append(mat_bottom)
    
    return lake_surface, lake_bottom

def generate_lakes():
    """Genera lagos a lo largo del cañón"""
    print("💧 Generando lagos...")
    
    random.seed(WorldConfig.SEED + 200)
    lakes = []
    
    for i in range(WorldConfig.LAKE_COUNT):
        x = random.uniform(-WorldConfig.CANYON_LENGTH/2 + 50, WorldConfig.CANYON_LENGTH/2 - 50)
        y = random.uniform(-10, 10)
        z = 0
        
        size = random.uniform(15, 30)
        
        lake_surface, lake_bottom = create_lake((x, y, z), size)
        lake_surface.name = f"Lake_{i+1}_Surface"
        lake_bottom.name = f"Lake_{i+1}_Bottom"
        lakes.append((lake_surface, lake_bottom))
    
    print(f"✓ {len(lakes)} lagos generados")
    return lakes

# ============================================================================
# CRIATURAS ACUÁTICAS ALIENÍGENAS
# ============================================================================

def create_alien_creature(location, size=3):
    """Crea una criatura acuática alienígena"""
    
    # Cuerpo principal (elipsoide)
    bpy.ops.mesh.primitive_uv_sphere_add(
        radius=size,
        location=location
    )
    creature = bpy.context.active_object
    creature.name = "Alien_Creature"
    creature.scale = (1.5, 1, 0.8)  # Forma de pez
    bpy.ops.object.transform_apply(scale=True)
    
    # Añadir aletas
    bpy.ops.mesh.primitive_cone_add(
        radius1=size * 0.5,
        radius2=0,
        depth=size * 0.8,
        location=(location[0] - size * 1.2, location[1], location[2])
    )
    tail = bpy.context.active_object
    tail.rotation_euler[1] = math.radians(90)
    
    # Unir cuerpo y cola
    bpy.ops.object.select_all(action='DESELECT')
    creature.select_set(True)
    tail.select_set(True)
    bpy.context.view_layer.objects.active = creature
    bpy.ops.object.join()
    
    # Material bioluminiscente
    mat_creature = create_material(
        f"Creature_{random.randint(1000, 9999)}",
        color=(random.uniform(0.3, 0.8), random.uniform(0.2, 0.6), random.uniform(0.6, 1.0), 1.0),
        metallic=0.3,
        roughness=0.4,
        emission=random.uniform(0.5, 2.0),
        emission_color=(random.uniform(0.3, 1.0), random.uniform(0.3, 0.8), random.uniform(0.7, 1.0), 1.0)
    )
    creature.data.materials.append(mat_creature)
    
    # Añadir animación de nado (keyframes básicos)
    creature.animation_data_create()
    creature.animation_data.action = bpy.data.actions.new(name=f"Swim_{creature.name}")
    
    # Animación de movimiento ondulante
    for frame in range(0, 250, 25):
        creature.location.z = location[2] + math.sin(frame * 0.1) * 2
        creature.rotation_euler[2] = math.sin(frame * 0.05) * 0.2
        creature.keyframe_insert(data_path="location", frame=frame)
        creature.keyframe_insert(data_path="rotation_euler", frame=frame)
    
    return creature

def generate_creatures():
    """Genera criaturas acuáticas cerca de los lagos"""
    print("🐋 Generando criaturas alienígenas...")
    
    random.seed(WorldConfig.SEED + 300)
    creatures = []
    
    for i in range(WorldConfig.CREATURE_COUNT):
        x = random.uniform(-WorldConfig.CANYON_LENGTH/2 + 50, WorldConfig.CANYON_LENGTH/2 - 50)
        y = random.uniform(-10, 10)
        z = random.uniform(-2, 4)  # Algunas bajo agua, otras saltando
        
        size = random.uniform(2, 5)
        
        creature = create_alien_creature((x, y, z), size)
        creature.name = f"Creature_{i+1}"
        creatures.append(creature)
    
    print(f"✓ {len(creatures)} criaturas generadas")
    return creatures

# ============================================================================
# ILUMINACIÓN Y ATMÓSFERA
# ============================================================================

def setup_lighting():
    """Configura la iluminación sci-fi del mundo"""
    print("💡 Configurando iluminación...")
    
    # Sol alienígena (luz principal)
    bpy.ops.object.light_add(type='SUN', location=(0, 0, 100))
    sun = bpy.context.active_object
    sun.name = "Alien_Sun"
    sun.data.energy = 3.0
    sun.data.color = (0.9, 0.7, 1.0)  # Luz violeta
    sun.rotation_euler = (math.radians(45), 0, math.radians(30))
    
    # Luz ambiental (HDRI o color de mundo)
    world = bpy.context.scene.world
    world.use_nodes = True
    nodes = world.node_tree.nodes
    links = world.node_tree.links
    nodes.clear()
    
    node_background = nodes.new(type='ShaderNodeBackground')
    node_output = nodes.new(type='ShaderNodeOutputWorld')
    
    # Color de cielo alienígena (azul-morado)
    node_background.inputs['Color'].default_value = (0.3, 0.2, 0.5, 1.0)
    node_background.inputs['Strength'].default_value = 0.5
    
    links.new(node_background.outputs['Background'], node_output.inputs['Surface'])
    
    # Luces volumétricas (niebla alienígena)
    bpy.context.scene.world.mist_settings.use_mist = True
    bpy.context.scene.world.mist_settings.intensity = 0.3
    bpy.context.scene.world.mist_settings.start = 50
    bpy.context.scene.world.mist_settings.depth = 200
    
    print("✓ Iluminación configurada")

def setup_camera_path():
    """Crea un camino para la cámara (vuelo de la nave)"""
    print("🎥 Configurando cámara...")
    
    # Crear cámara
    bpy.ops.object.camera_add(location=(-WorldConfig.CANYON_LENGTH/2, 0, 20))
    camera = bpy.context.active_object
    camera.name = "Ship_Camera"
    bpy.context.scene.camera = camera
    
    # Crear curva de vuelo
    curve_data = bpy.data.curves.new(name="Flight_Path", type='CURVE')
    curve_data.dimensions = '3D'
    
    spline = curve_data.splines.new(type='BEZIER')
    spline.bezier_points.add(3)  # 4 puntos en total
    
    # Definir puntos de la curva (vuelo a través del cañón)
    points = [
        (-WorldConfig.CANYON_LENGTH/2, -10, 20),
        (-WorldConfig.CANYON_LENGTH/4, 10, 15),
        (WorldConfig.CANYON_LENGTH/4, -5, 25),
        (WorldConfig.CANYON_LENGTH/2, 5, 18)
    ]
    
    for i, point in enumerate(points):
        spline.bezier_points[i].co = point
        spline.bezier_points[i].handle_left_type = 'AUTO'
        spline.bezier_points[i].handle_right_type = 'AUTO'
    
    curve_obj = bpy.data.objects.new("Flight_Path", curve_data)
    bpy.context.collection.objects.link(curve_obj)
    
    # Hacer que la cámara siga la curva
    constraint = camera.constraints.new(type='FOLLOW_PATH')
    constraint.target = curve_obj
    constraint.use_curve_follow = True
    
    # Animar el movimiento (500 frames)
    curve_obj.data.path_duration = 500
    camera.constraints["Follow Path"].offset = 0
    camera.keyframe_insert(data_path='constraints["Follow Path"].offset', frame=1)
    camera.constraints["Follow Path"].offset = -100
    camera.keyframe_insert(data_path='constraints["Follow Path"].offset', frame=500)
    
    print("✓ Cámara configurada con animación de vuelo")
    return camera

# ============================================================================
# CONFIGURACIÓN DE RENDERIZADO
# ============================================================================

def setup_render_settings():
    """Configura los ajustes de renderizado para calidad óptima"""
    print("⚙️  Configurando renderizado...")
    
    scene = bpy.context.scene
    
    # Motor de render: Cycles para mejor calidad
    scene.render.engine = 'CYCLES'
    scene.cycles.samples = 128
    scene.cycles.use_denoising = True
    
    # Resolución
    scene.render.resolution_x = 1920
    scene.render.resolution_y = 1080
    scene.render.resolution_percentage = 100
    
    # Frame range para animación
    scene.frame_start = 1
    scene.frame_end = 500
    scene.frame_current = 1
    
    print("✓ Renderizado configurado (Cycles, 1920x1080, 128 samples)")

# ============================================================================
# FUNCIÓN PRINCIPAL
# ============================================================================

def generate_alien_world():
    """Función principal que genera todo el mundo alienígena"""
    print("\n" + "="*60)
    print("🌌 GENERANDO MUNDO SCI-FI ALIENÍGENA")
    print("="*60 + "\n")
    
    # Limpiar escena
    clear_scene()
    
    # Generar elementos del mundo
    canyon = create_canyon_terrain()
    buildings = generate_alien_buildings()
    trees = generate_forest()
    lakes = generate_lakes()
    creatures = generate_creatures()
    
    # Configurar iluminación y cámara
    setup_lighting()
    camera = setup_camera_path()
    
    # Configurar renderizado
    setup_render_settings()
    
    print("\n" + "="*60)
    print("✅ MUNDO GENERADO EXITOSAMENTE")
    print("="*60)
    print(f"""
📊 ESTADÍSTICAS:
   • Cañón: {WorldConfig.CANYON_LENGTH}m × {WorldConfig.CANYON_WIDTH}m
   • Edificios: {len(buildings)}
   • Árboles: {len(trees)}
   • Lagos: {len(lakes)}
   • Criaturas: {len(creatures)}
   
🎬 ANIMACIÓN:
   • Duración: 500 frames (~20 segundos a 24fps)
   • Cámara configurada para vuelo de nave
   
💡 SIGUIENTE PASO:
   • Presiona Espacio para reproducir la animación
   • Renderiza con F12 o Render > Render Animation
    """)

# ============================================================================
# EJECUTAR
# ============================================================================

if __name__ == "__main__":
    generate_alien_world()
