"""
Generador de Personaje 3D para Blender
Personaje: Mujer de 60 años, 1.75m, 80kg, pelo corto blanco, gafas
Pose: T-pose (brazos abiertos, piernas separadas)
Autor: GitHub Copilot
Fecha: Noviembre 2025
"""

import bpy
import bmesh
import math
from mathutils import Vector

# ============================================================================
# CONFIGURACIÓN DEL PERSONAJE
# ============================================================================

class CharacterConfig:
    """Configuración del personaje"""
    HEIGHT = 1.75           # Altura en metros
    WEIGHT = 80             # Peso en kg (afecta proporciones)
    AGE = 60                # Edad (afecta detalles)
    
    # Proporciones corporales (basadas en altura)
    HEAD_HEIGHT = HEIGHT / 7.5
    TORSO_HEIGHT = HEIGHT * 0.35
    LEG_HEIGHT = HEIGHT * 0.45
    ARM_LENGTH = HEIGHT * 0.40
    
    # Grosor (ajustado por peso)
    TORSO_WIDTH = 0.35
    WAIST_WIDTH = 0.30
    HIP_WIDTH = 0.38
    LEG_THICKNESS = 0.12
    ARM_THICKNESS = 0.08
    
    # Colores
    SKIN_COLOR = (0.95, 0.87, 0.80, 1.0)  # Piel madura
    HAIR_COLOR = (0.95, 0.95, 0.95, 1.0)  # Blanco
    GLASSES_COLOR = (0.1, 0.1, 0.1, 1.0)  # Negro
    CLOTHING_COLOR = (0.4, 0.5, 0.7, 1.0) # Azul suave

# ============================================================================
# UTILIDADES
# ============================================================================

def clear_scene():
    """Limpia la escena de Blender"""
    bpy.ops.object.select_all(action='SELECT')
    bpy.ops.object.delete(use_global=False)
    
    for material in bpy.data.materials:
        if not material.users:
            bpy.data.materials.remove(material)
    
    print("✓ Escena limpiada")

def create_material(name, color, roughness=0.5, subsurface=0.0):
    """Crea un material PBR"""
    mat = bpy.data.materials.new(name=name)
    mat.use_nodes = True
    nodes = mat.node_tree.nodes
    nodes.clear()
    
    node_principled = nodes.new(type='ShaderNodeBsdfPrincipled')
    node_output = nodes.new(type='ShaderNodeOutputMaterial')
    
    node_principled.inputs['Base Color'].default_value = color
    node_principled.inputs['Roughness'].default_value = roughness
    
    # Intentar aplicar subsurface solo si existe (versiones nuevas de Blender)
    try:
        if 'Subsurface' in node_principled.inputs:
            node_principled.inputs['Subsurface'].default_value = subsurface
            if subsurface > 0:
                node_principled.inputs['Subsurface Color'].default_value = color
        elif 'Subsurface Weight' in node_principled.inputs:  # Blender 4.0+
            node_principled.inputs['Subsurface Weight'].default_value = subsurface
    except:
        pass  # Si falla, continuar sin subsurface
    
    mat.node_tree.links.new(node_principled.outputs['BSDF'], node_output.inputs['Surface'])
    
    return mat

# ============================================================================
# CABEZA Y ROSTRO
# ============================================================================

def create_head():
    """Crea la cabeza con proporciones de mujer mayor y cuello"""
    print("👤 Creando cabeza y cuello...")
    
    head_center_z = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT / 2
    neck_z = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT
    
    # CUELLO primero
    neck_height = 0.12
    neck_radius = 0.045
    bpy.ops.mesh.primitive_cylinder_add(
        vertices=16,
        radius=neck_radius,
        depth=neck_height,
        location=(0, 0, neck_z - neck_height / 2)
    )
    neck = bpy.context.active_object
    neck.name = "Neck"
    
    # Material de piel para cuello
    mat_skin_neck = create_material(
        "Skin_Neck",
        CharacterConfig.SKIN_COLOR,
        roughness=0.4,
        subsurface=0.15
    )
    neck.data.materials.append(mat_skin_neck)
    
    # CABEZA (esfera con más segmentos para mejor UV mapping)
    bpy.ops.mesh.primitive_uv_sphere_add(
        segments=64,  # Más segmentos para mejor textura
        ring_count=32,
        radius=CharacterConfig.HEAD_HEIGHT / 2,
        location=(0, 0, head_center_z)
    )
    head = bpy.context.active_object
    head.name = "Head"
    
    # Forma más realista: un poco más ancha y menos alta
    head.scale = (0.95, 0.85, 1.02)
    bpy.ops.object.transform_apply(scale=True)
    
    # Modificar geometría para forma más humana
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.select_all(action='SELECT')
    bpy.ops.object.mode_set(mode='OBJECT')
    
    mesh = head.data
    for vertex in mesh.vertices:
        x, y, z = vertex.co
        
        # Aplanar un poco la parte posterior (nuca)
        if y < 0:
            vertex.co.y *= 0.95
        
        # Frente ligeramente más redondeada
        if y > 0 and z > 0:
            vertex.co.y *= 1.05
    
    # Unir cuello y cabeza
    bpy.ops.object.select_all(action='DESELECT')
    neck.select_set(True)
    head.select_set(True)
    bpy.context.view_layer.objects.active = head
    bpy.ops.object.join()
    
    head = bpy.context.active_object
    head.name = "Head"
    
    # ROTAR la cabeza 180° para que mire al frente (las gafas adelante)
    head.rotation_euler[2] = math.radians(180)
    
    # APLICAR TEXTURA DE LA IMAGEN
    mat_face = bpy.data.materials.new(name="Face_Texture")
    mat_face.use_nodes = True
    nodes = mat_face.node_tree.nodes
    links = mat_face.node_tree.links
    nodes.clear()
    
    # Nodos para textura de imagen
    node_tex_coord = nodes.new(type='ShaderNodeTexCoord')
    node_mapping = nodes.new(type='ShaderNodeMapping')
    node_image = nodes.new(type='ShaderNodeTexImage')
    node_principled = nodes.new(type='ShaderNodeBsdfPrincipled')
    node_output = nodes.new(type='ShaderNodeOutputMaterial')
    
    # Cargar la imagen
    import os
    image_path = r"C:\Users\matel\OneDrive\Imágenes\Inés\face.png"
    
    if os.path.exists(image_path):
        try:
            image = bpy.data.images.load(image_path)
            node_image.image = image
            print(f"✓ Textura cargada: {image_path}")
        except:
            print(f"⚠️ No se pudo cargar la imagen: {image_path}")
    else:
        print(f"⚠️ Imagen no encontrada: {image_path}")
    
    # Conectar nodos
    links.new(node_tex_coord.outputs['UV'], node_mapping.inputs['Vector'])
    links.new(node_mapping.outputs['Vector'], node_image.inputs['Vector'])
    links.new(node_image.outputs['Color'], node_principled.inputs['Base Color'])
    links.new(node_principled.outputs['BSDF'], node_output.inputs['Surface'])
    
    # Ajustar el mapping para que la textura esté bien orientada
    # Rotar la textura 180° en Z para que coincida con la rotación de la cabeza
    node_mapping.inputs['Rotation'].default_value[2] = math.radians(180)
    
    # Ajustes del material
    node_principled.inputs['Roughness'].default_value = 0.4
    
    # Aplicar material a la cabeza
    head.data.materials.append(mat_face)
    
    # Subdivisión fuerte para suavidad
    mod_subsurf = head.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 2
    mod_subsurf.render_levels = 3
    
    print("✓ Cabeza, cuello y textura creados")
    return head

def create_hair():
    """Crea el pelo corto blanco"""
    print("💇 Creando cabello...")
    
    head_height = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT / 2
    
    # Pelo base (casquete superior)
    bpy.ops.mesh.primitive_uv_sphere_add(
        radius=CharacterConfig.HEAD_HEIGHT / 2 * 1.05,
        location=(0, 0, head_height)
    )
    hair = bpy.context.active_object
    hair.name = "Hair"
    hair.scale = (1.0, 0.9, 0.7)  # Pelo corto
    bpy.ops.object.transform_apply(scale=True)
    
    # Cortar la parte inferior del pelo
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.select_all(action='DESELECT')
    
    bpy.ops.object.mode_set(mode='OBJECT')
    mesh = hair.data
    for vertex in mesh.vertices:
        if vertex.co.z < head_height - 0.02:
            vertex.select = True
    
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.delete(type='VERT')
    bpy.ops.object.mode_set(mode='OBJECT')
    
    # ROTAR el pelo 180° para que coincida con la cabeza
    hair.rotation_euler[2] = math.radians(180)
    
    # Material de pelo blanco
    mat_hair = create_material(
        "Hair",
        CharacterConfig.HAIR_COLOR,
        roughness=0.6
    )
    hair.data.materials.append(mat_hair)
    
    # Subdivisión
    mod_subsurf = hair.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 1
    
    print("✓ Cabello creado")
    return hair

def create_glasses():
    """Crea las gafas"""
    print("👓 Creando gafas...")
    
    head_z = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT / 2
    # Posición POSITIVA en Y para que después de rotar 180° queden ADELANTE
    face_y = CharacterConfig.HEAD_HEIGHT / 2 * 0.85  # POSITIVO - quedará adelante tras rotar
    eye_z = head_z + 0.01  # Altura de ojos
    
    # Lente izquierda (desde el punto de vista del personaje)
    bpy.ops.mesh.primitive_torus_add(
        major_radius=0.035,
        minor_radius=0.004,
        location=(0.035, face_y, eye_z)  # Positivo X para izquierda del personaje
    )
    left_lens = bpy.context.active_object
    left_lens.name = "Glasses_Left"
    left_lens.rotation_euler[0] = math.radians(90)
    
    # Lente derecha
    bpy.ops.mesh.primitive_torus_add(
        major_radius=0.035,
        minor_radius=0.004,
        location=(-0.035, face_y, eye_z)  # Negativo X para derecha del personaje
    )
    right_lens = bpy.context.active_object
    right_lens.name = "Glasses_Right"
    right_lens.rotation_euler[0] = math.radians(90)
    
    # Puente entre lentes (nariz)
    bpy.ops.mesh.primitive_cylinder_add(
        radius=0.003,
        depth=0.07,
        location=(0, face_y, eye_z)
    )
    bridge = bpy.context.active_object
    bridge.name = "Glasses_Bridge"
    bridge.rotation_euler[1] = math.radians(90)
    
    # Patilla izquierda (va hacia atrás = Y negativo después de rotar)
    bpy.ops.mesh.primitive_cylinder_add(
        radius=0.003,
        depth=0.10,
        location=(0.065, face_y - 0.05, eye_z)  # Y menos = hacia atrás tras rotar
    )
    left_arm = bpy.context.active_object
    left_arm.name = "Glasses_Arm_Left"
    left_arm.rotation_euler[2] = math.radians(90)
    
    # Patilla derecha (va hacia atrás = Y negativo después de rotar)
    bpy.ops.mesh.primitive_cylinder_add(
        radius=0.003,
        depth=0.10,
        location=(-0.065, face_y - 0.05, eye_z)
    )
    right_arm = bpy.context.active_object
    right_arm.name = "Glasses_Arm_Right"
    right_arm.rotation_euler[2] = math.radians(90)
    
    # Unir todas las partes
    bpy.ops.object.select_all(action='DESELECT')
    left_lens.select_set(True)
    right_lens.select_set(True)
    bridge.select_set(True)
    left_arm.select_set(True)
    right_arm.select_set(True)
    bpy.context.view_layer.objects.active = left_lens
    bpy.ops.object.join()
    
    glasses = bpy.context.active_object
    glasses.name = "Glasses"
    
    # NO rotar las gafas - ya están en la posición correcta
    # La rotación de la cabeza hará que queden adelante
    
    # Material de las gafas
    mat_glasses = create_material(
        "Glasses_Frame",
        CharacterConfig.GLASSES_COLOR,
        roughness=0.3
    )
    glasses.data.materials.append(mat_glasses)
    
    print("✓ Gafas creadas")
    return glasses

# ============================================================================
# TORSO
# ============================================================================

def create_torso():
    """Crea el torso con forma femenina realista"""
    print("👕 Creando torso...")
    
    neck_z = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT
    torso_bottom = neck_z - CharacterConfig.TORSO_HEIGHT
    torso_center_z = (neck_z + torso_bottom) / 2
    
    # Torso usando cubo con muchas subdivisiones
    bpy.ops.mesh.primitive_cube_add(
        size=1,
        location=(0, 0, torso_center_z)
    )
    torso = bpy.context.active_object
    torso.name = "Torso"
    
    # Escalar para dar forma de torso femenino
    torso.scale = (
        CharacterConfig.TORSO_WIDTH,
        CharacterConfig.TORSO_WIDTH * 0.55,
        CharacterConfig.TORSO_HEIGHT
    )
    bpy.ops.object.transform_apply(scale=True)
    
    # Entrar en modo edición para dar forma orgánica
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.select_all(action='SELECT')
    
    # Añadir MUCHAS subdivisiones para forma suave
    bpy.ops.mesh.subdivide(number_cuts=4)
    bpy.ops.object.mode_set(mode='OBJECT')
    
    # Ajustar forma vértice por vértice para silueta femenina
    mesh = torso.data
    for vertex in mesh.vertices:
        # Normalizar posición Z (0 = abajo, 1 = arriba)
        z_factor = (vertex.co.z - torso_bottom) / CharacterConfig.TORSO_HEIGHT
        
        # Distancia desde el centro (para control radial)
        dist_from_center = math.sqrt(vertex.co.x**2 + vertex.co.y**2)
        
        # HOMBROS (parte superior) - más anchos
        if z_factor > 0.85:
            scale_factor = 1.15
            vertex.co.x *= scale_factor
            vertex.co.y *= scale_factor * 0.9
        
        # PECHO (parte superior-media)
        elif 0.7 < z_factor <= 0.85:
            if vertex.co.y < 0:  # Parte frontal
                vertex.co.y *= 1.2
            scale_factor = 1.05
            vertex.co.x *= scale_factor
        
        # CINTURA (parte media) - más estrecha
        elif 0.4 < z_factor <= 0.7:
            scale_factor = 0.75 + (z_factor - 0.4) * 0.5
            vertex.co.x *= scale_factor
            vertex.co.y *= scale_factor
        
        # CADERAS (parte inferior) - más anchas que cintura
        elif 0.15 < z_factor <= 0.4:
            scale_factor = 0.75 + (0.4 - z_factor) * 1.5
            vertex.co.x *= scale_factor
            if vertex.co.y > 0:  # Parte trasera
                vertex.co.y *= 1.1
        
        # BASE (muy inferior) - conexión con piernas
        else:
            scale_factor = 1.0
            vertex.co.x *= scale_factor
    
    # Material de ropa
    mat_clothing = create_material(
        "Clothing",
        CharacterConfig.CLOTHING_COLOR,
        roughness=0.7
    )
    torso.data.materials.append(mat_clothing)
    
    # Subdivisión fuerte para suavizar todas las curvas
    mod_subsurf = torso.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 3
    mod_subsurf.render_levels = 4
    
    print("✓ Torso creado")
    return torso

# ============================================================================
# BRAZOS
# ============================================================================

def create_arm(side='left'):
    """Crea un brazo (izquierdo o derecho)"""
    
    # sign = -1 para izquierda (X negativo), +1 para derecha (X positivo)
    sign = -1 if side == 'left' else 1
    shoulder_z = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT - 0.08
    # AUMENTAR la separación desde el hombro
    shoulder_x = sign * (CharacterConfig.TORSO_WIDTH / 2 + 0.05)
    
    print(f"  → Creando brazo {side}: hombro en X={shoulder_x:.3f}")
    
    # Brazo superior (horizontal en T-pose)
    upper_arm_length = CharacterConfig.ARM_LENGTH * 0.48
    arm_center_x = shoulder_x + sign * upper_arm_length / 2
    
    print(f"    → Centro brazo en X={arm_center_x:.3f}")
    
    bpy.ops.mesh.primitive_cylinder_add(
        vertices=16,
        radius=CharacterConfig.ARM_THICKNESS,
        depth=upper_arm_length,
        location=(
            arm_center_x,
            0,
            shoulder_z
        )
    )
    upper_arm = bpy.context.active_object
    upper_arm.name = f"UpperArm_{side.capitalize()}"
    # Rotar 90° en el eje Y para que quede horizontal
    upper_arm.rotation_euler[1] = math.radians(sign * 90)
    
    # Codo (articulación)
    bpy.ops.mesh.primitive_uv_sphere_add(
        radius=CharacterConfig.ARM_THICKNESS * 0.95,
        location=(
            shoulder_x + sign * upper_arm_length,
            0,
            shoulder_z
        )
    )
    elbow = bpy.context.active_object
    elbow.name = f"Elbow_{side.capitalize()}"
    
    # Brazo inferior (antebrazo)
    lower_arm_length = CharacterConfig.ARM_LENGTH * 0.45
    bpy.ops.mesh.primitive_cylinder_add(
        vertices=16,
        radius=CharacterConfig.ARM_THICKNESS * 0.85,
        depth=lower_arm_length,
        location=(
            shoulder_x + sign * (upper_arm_length + lower_arm_length / 2),
            0,
            shoulder_z
        )
    )
    lower_arm = bpy.context.active_object
    lower_arm.name = f"LowerArm_{side.capitalize()}"
    lower_arm.rotation_euler[1] = math.radians(sign * 90)
    
    # Muñeca
    bpy.ops.mesh.primitive_uv_sphere_add(
        radius=CharacterConfig.ARM_THICKNESS * 0.7,
        location=(
            shoulder_x + sign * (upper_arm_length + lower_arm_length),
            0,
            shoulder_z
        )
    )
    wrist = bpy.context.active_object
    wrist.name = f"Wrist_{side.capitalize()}"
    
    # Mano (más detallada)
    hand_length = 0.09
    hand_width = 0.075
    hand_thickness = 0.025
    bpy.ops.mesh.primitive_cube_add(
        size=1,
        location=(
            shoulder_x + sign * (upper_arm_length + lower_arm_length + hand_length / 2),
            0,
            shoulder_z
        )
    )
    hand = bpy.context.active_object
    hand.name = f"Hand_{side.capitalize()}"
    hand.scale = (hand_length, hand_width, hand_thickness)
    bpy.ops.object.transform_apply(scale=True)
    
    # Redondear la mano
    mod_subsurf_hand = hand.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf_hand.levels = 1
    
    # Unir partes del brazo
    bpy.ops.object.select_all(action='DESELECT')
    upper_arm.select_set(True)
    elbow.select_set(True)
    lower_arm.select_set(True)
    wrist.select_set(True)
    hand.select_set(True)
    bpy.context.view_layer.objects.active = upper_arm
    bpy.ops.object.join()
    
    arm = bpy.context.active_object
    arm.name = f"Arm_{side.capitalize()}"
    
    # Material de piel
    mat_skin = create_material(
        f"Skin_{side}",
        CharacterConfig.SKIN_COLOR,
        roughness=0.4,
        subsurface=0.15
    )
    arm.data.materials.append(mat_skin)
    
    # Subdivisión para suavizar
    mod_subsurf = arm.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 2
    mod_subsurf.render_levels = 3
    
    return arm

def create_arms():
    """Crea ambos brazos separados"""
    print("💪 Creando brazos...")
    
    left_arm = create_arm('left')
    print(f"  ✓ Brazo izquierdo creado en X={left_arm.location.x}")
    
    right_arm = create_arm('right')
    print(f"  ✓ Brazo derecho creado en X={right_arm.location.x}")
    
    print("✓ Ambos brazos creados")
    return left_arm, right_arm

# ============================================================================
# PIERNAS
# ============================================================================

def create_leg(side='left'):
    """Crea una pierna (izquierda o derecha) mejorada"""
    
    sign = -1 if side == 'left' else 1
    hip_z = CharacterConfig.HEIGHT - CharacterConfig.HEAD_HEIGHT - CharacterConfig.TORSO_HEIGHT
    # AUMENTAR la separación entre piernas
    hip_x = sign * CharacterConfig.HIP_WIDTH / 2.5
    
    print(f"  → Creando pierna {side}: cadera en X={hip_x:.3f}")
    
    # Muslo (más grueso arriba, más fino abajo)
    thigh_length = CharacterConfig.LEG_HEIGHT * 0.52
    bpy.ops.mesh.primitive_cylinder_add(
        vertices=16,
        radius=CharacterConfig.LEG_THICKNESS,
        depth=thigh_length,
        location=(hip_x, 0, hip_z - thigh_length / 2)
    )
    thigh = bpy.context.active_object
    thigh.name = f"Thigh_{side.capitalize()}"
    
    # Modificar el muslo para que sea más grueso arriba
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.select_all(action='SELECT')
    bpy.ops.object.mode_set(mode='OBJECT')
    
    mesh = thigh.data
    for vertex in mesh.vertices:
        if vertex.co.z > 0:  # Parte superior
            scale = 1.2
            vertex.co.x *= scale
            vertex.co.y *= scale
    
    # Rodilla (articulación)
    knee_z = hip_z - thigh_length
    bpy.ops.mesh.primitive_uv_sphere_add(
        radius=CharacterConfig.LEG_THICKNESS * 0.9,
        location=(hip_x, 0, knee_z)
    )
    knee = bpy.context.active_object
    knee.name = f"Knee_{side.capitalize()}"
    knee.scale = (1.0, 1.0, 0.9)
    bpy.ops.object.transform_apply(scale=True)
    
    # Pantorrilla (más delgada que el muslo)
    calf_length = CharacterConfig.LEG_HEIGHT * 0.48
    bpy.ops.mesh.primitive_cylinder_add(
        vertices=16,
        radius=CharacterConfig.LEG_THICKNESS * 0.75,
        depth=calf_length,
        location=(hip_x, 0, knee_z - calf_length / 2)
    )
    calf = bpy.context.active_object
    calf.name = f"Calf_{side.capitalize()}"
    
    # Tobillo
    ankle_z = knee_z - calf_length
    bpy.ops.mesh.primitive_uv_sphere_add(
        radius=CharacterConfig.LEG_THICKNESS * 0.6,
        location=(hip_x, 0, ankle_z)
    )
    ankle = bpy.context.active_object
    ankle.name = f"Ankle_{side.capitalize()}"
    
    # Pie (más realista)
    foot_length = 0.22
    foot_height = 0.09
    foot_width = 0.08
    bpy.ops.mesh.primitive_cube_add(
        size=1,
        location=(hip_x, 0.06, foot_height / 2)
    )
    foot = bpy.context.active_object
    foot.name = f"Foot_{side.capitalize()}"
    foot.scale = (foot_width, foot_length, foot_height)
    bpy.ops.object.transform_apply(scale=True)
    
    # Redondear el pie
    bpy.ops.object.mode_set(mode='EDIT')
    bpy.ops.mesh.select_all(action='SELECT')
    bpy.ops.mesh.subdivide(number_cuts=1)
    bpy.ops.object.mode_set(mode='OBJECT')
    
    # Unir partes de la pierna
    bpy.ops.object.select_all(action='DESELECT')
    thigh.select_set(True)
    knee.select_set(True)
    calf.select_set(True)
    ankle.select_set(True)
    foot.select_set(True)
    bpy.context.view_layer.objects.active = thigh
    bpy.ops.object.join()
    
    leg = bpy.context.active_object
    leg.name = f"Leg_{side.capitalize()}"
    
    # Material de piel
    mat_skin = create_material(
        f"Skin_Leg_{side}",
        CharacterConfig.SKIN_COLOR,
        roughness=0.4,
        subsurface=0.15
    )
    leg.data.materials.append(mat_skin)
    
    # Subdivisión fuerte
    mod_subsurf = leg.modifiers.new(name="Subdivision", type='SUBSURF')
    mod_subsurf.levels = 3
    mod_subsurf.render_levels = 4
    
    return leg

def create_legs():
    """Crea ambas piernas separadas"""
    print("🦵 Creando piernas...")
    
    left_leg = create_leg('left')
    print(f"  ✓ Pierna izquierda creada en X={left_leg.location.x}")
    
    right_leg = create_leg('right')
    print(f"  ✓ Pierna derecha creada en X={right_leg.location.x}")
    
    print("✓ Ambas piernas creadas")
    return left_leg, right_leg

# ============================================================================
# ILUMINACIÓN Y CÁMARA
# ============================================================================

def setup_lighting():
    """Configura iluminación de tres puntos"""
    print("💡 Configurando iluminación...")
    
    # Luz principal (Key light)
    bpy.ops.object.light_add(type='AREA', location=(2, -3, 2.5))
    key_light = bpy.context.active_object
    key_light.name = "Key_Light"
    key_light.data.energy = 200
    key_light.data.size = 2
    key_light.rotation_euler = (math.radians(60), 0, math.radians(45))
    
    # Luz de relleno (Fill light)
    bpy.ops.object.light_add(type='AREA', location=(-2, -2, 1.5))
    fill_light = bpy.context.active_object
    fill_light.name = "Fill_Light"
    fill_light.data.energy = 100
    fill_light.data.size = 2
    fill_light.rotation_euler = (math.radians(45), 0, math.radians(-45))
    
    # Luz trasera (Rim light)
    bpy.ops.object.light_add(type='AREA', location=(0, 2, 2))
    rim_light = bpy.context.active_object
    rim_light.name = "Rim_Light"
    rim_light.data.energy = 150
    rim_light.data.size = 1.5
    rim_light.rotation_euler = (math.radians(120), 0, 0)
    
    # Configurar mundo (fondo)
    world = bpy.context.scene.world
    world.use_nodes = True
    world.node_tree.nodes["Background"].inputs[0].default_value = (0.8, 0.8, 0.85, 1.0)
    world.node_tree.nodes["Background"].inputs[1].default_value = 0.3
    
    print("✓ Iluminación configurada")

def setup_camera():
    """Configura la cámara para vista frontal del personaje"""
    print("🎥 Configurando cámara...")
    
    bpy.ops.object.camera_add(location=(0, -3, CharacterConfig.HEIGHT / 2))
    camera = bpy.context.active_object
    camera.name = "Main_Camera"
    camera.rotation_euler = (math.radians(90), 0, 0)
    
    bpy.context.scene.camera = camera
    
    # Ajustes de cámara
    camera.data.lens = 50  # Focal length más natural para retrato
    camera.data.clip_end = 100
    
    print("✓ Cámara configurada")
    return camera

# ============================================================================
# CONFIGURACIÓN DE RENDER
# ============================================================================

def setup_render_settings():
    """Configura los ajustes de renderizado"""
    print("⚙️  Configurando renderizado...")
    
    scene = bpy.context.scene
    
    # Motor Cycles para mejor calidad
    scene.render.engine = 'CYCLES'
    scene.cycles.samples = 256
    scene.cycles.use_denoising = True
    
    # Resolución
    scene.render.resolution_x = 1920
    scene.render.resolution_y = 1080
    scene.render.resolution_percentage = 100
    
    print("✓ Renderizado configurado")

# ============================================================================
# FUNCIÓN PRINCIPAL
# ============================================================================

def create_character():
    """Función principal que genera el personaje completo"""
    print("\n" + "="*60)
    print("👵 GENERANDO PERSONAJE: MUJER DE 60 AÑOS")
    print("="*60 + "\n")
    
    # Limpiar escena
    clear_scene()
    
    # Crear partes del cuerpo
    try:
        head = create_head()
        print(f"  Cabeza en: {head.location}")
    except Exception as e:
        print(f"❌ Error creando cabeza: {e}")
    
    try:
        hair = create_hair()
        print(f"  Pelo en: {hair.location}")
    except Exception as e:
        print(f"❌ Error creando pelo: {e}")
    
    try:
        glasses = create_glasses()
        print(f"  Gafas en: {glasses.location}")
    except Exception as e:
        print(f"❌ Error creando gafas: {e}")
    
    try:
        torso = create_torso()
        print(f"  Torso en: {torso.location}")
    except Exception as e:
        print(f"❌ Error creando torso: {e}")
    
    try:
        left_arm, right_arm = create_arms()
        print(f"  Brazo izq en: {left_arm.location}")
        print(f"  Brazo der en: {right_arm.location}")
    except Exception as e:
        print(f"❌ Error creando brazos: {e}")
    
    try:
        left_leg, right_leg = create_legs()
        print(f"  Pierna izq en: {left_leg.location}")
        print(f"  Pierna der en: {right_leg.location}")
    except Exception as e:
        print(f"❌ Error creando piernas: {e}")
    
    # Configurar escena
    setup_lighting()
    camera = setup_camera()
    setup_render_settings()
    
    print("\n" + "="*60)
    print("✅ PERSONAJE CREADO EXITOSAMENTE")
    print("="*60)
    print(f"""
📊 ESPECIFICACIONES:
   • Altura: {CharacterConfig.HEIGHT}m
   • Peso: {CharacterConfig.WEIGHT}kg
   • Edad: {CharacterConfig.AGE} años
   • Pelo: Corto, blanco
   • Accesorios: Gafas
   • Pose: T-pose (brazos abiertos)
   
🎨 COMPONENTES:
   • Cabeza con subdivisión
   • Cabello corto
   • Gafas completas
   • Torso con forma femenina
   • Brazos (izquierdo y derecho)
   • Piernas (izquierda y derecha)
   
💡 SIGUIENTES PASOS:
   • Presiona Numpad 0 para ver desde la cámara
   • F12 para renderizar
   • Puedes añadir armature para animar
   • Ajusta colores en el Shader Editor si quieres
    """)

# ============================================================================
# EJECUTAR
# ============================================================================

if __name__ == "__main__":
    create_character()
