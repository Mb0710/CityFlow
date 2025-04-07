import * as THREE from "https://cdn.skypack.dev/three@0.129.0/build/three.module.js";
import { GLTFLoader } from "https://cdn.skypack.dev/three@0.129.0/examples/jsm/loaders/GLTFLoader.js";

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(90, window.innerWidth / window.innerHeight, 0.1, 1000);

let object;
let animationActive = true;

const loader = new GLTFLoader();


const cloudPositions = [
    { x: -10, y: -1, z: -19, action: () => { window.location.href = '/inscriptions'; } },
    {
        x: -3, y: -3, z: -20,
        action: () => {
            if (userRole === "débutant") {

            } else {
                window.location.href = '/Gestion';
            }
        }
    },
    {
        x: 4, y: -2, z: -20,
        action: () => {
            if (isAdmin == 1) {

            }
            else {
                window.location.href = '/admin';
            }
        }
    },
    { x: 13, y: -2, z: -20, action: () => { window.location.href = '/admin/ajout'; } },
    { x: 12, y: -10, z: -20, action: () => { window.location.href = '/dashboard'; } },
];


const cloudObjects = [];

const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();


loader.load(
    './assets/scene2.glb',
    function (gltf) {
        object = gltf.scene;

        const box = new THREE.Box3().setFromObject(object);
        const center = box.getCenter(new THREE.Vector3());

        object.position.set(0, -20, -20);

        const size = box.getSize(new THREE.Vector3());
        const maxDim = Math.max(size.x, size.y, size.z);
        const scale = 16.0 / maxDim;
        object.scale.set(scale, scale, scale);

        scene.add(object);

        camera.position.set(-1, -2, -10);

        animate();
    }
);


cloudPositions.forEach((position, index) => {
    loader.load(
        `./assets/cloud${index}.2.glb`,
        function (gltf) {
            const cloudObject = gltf.scene;


            cloudObject.userData = {
                index: index,
                action: position.action,
                label: position.label
            };

            const box = new THREE.Box3().setFromObject(cloudObject);

            cloudObject.position.set(position.x, position.y, position.z);

            cloudObject.rotation.y = Math.PI / 2;
            const size = box.getSize(new THREE.Vector3());
            const maxDim = Math.max(size.x, size.y, size.z);
            const scale = 6.0 / maxDim;
            cloudObject.scale.set(scale, scale, scale);


            cloudObject.traverse(function (child) {
                if (child.isMesh) {
                    child.userData = { cloudIndex: index };
                }
            });

            cloudObjects.push(cloudObject);
            scene.add(cloudObject);
        }
    );
});

const renderer = new THREE.WebGLRenderer({
    alpha: true,
    antialias: true
});
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setClearColor(0x000000, 0);
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;

document.getElementById("container3D").appendChild(renderer.domElement);

const mainLight = new THREE.DirectionalLight(0xffffff, 0.2);
mainLight.position.set(5, 3, 5);
mainLight.castShadow = true;
scene.add(mainLight);

const backLight = new THREE.DirectionalLight(0xffffff, 0.5);
backLight.position.set(-5, 2, -5);
scene.add(backLight);

function animate() {
    requestAnimationFrame(animate);

    if (object && animationActive) {
        object.rotation.y += 0.005;
    }

    const time = Date.now() * 0.001;

    cloudObjects.forEach((cloud, index) => {
        if (cloud) {
            cloud.position.y += Math.sin(time + index) * 0.003;
        }
    });

    renderer.render(scene, camera);
}

window.addEventListener("resize", function () {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});


function onMouseMove(event) {
    mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
    mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

    raycaster.setFromCamera(mouse, camera);


    const allObjects = [];
    scene.traverse((object) => {
        if (object.isMesh) {
            allObjects.push(object);
        }
    });

    const intersects = raycaster.intersectObjects(allObjects);


    document.body.style.cursor = 'default';

    if (intersects.length > 0) {

        const hitObject = intersects[0].object;
        if (hitObject.userData && hitObject.userData.cloudIndex !== undefined) {
            document.body.style.cursor = 'pointer';
        }
    }
}

window.addEventListener("mousemove", onMouseMove);

window.addEventListener("click", function (event) {


    mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
    mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

    raycaster.setFromCamera(mouse, camera);


    const allObjects = [];
    scene.traverse((object) => {
        if (object.isMesh) {
            allObjects.push(object);
        }
    });

    const intersects = raycaster.intersectObjects(allObjects);

    if (intersects.length > 0) {
        const hitObject = intersects[0].object;


        if (hitObject.userData && hitObject.userData.cloudIndex !== undefined) {
            const cloudIndex = hitObject.userData.cloudIndex;
            console.log("Nuage cliqué:", cloudPositions[cloudIndex].label);
            cloudPositions[cloudIndex].action();
        }
    }
});