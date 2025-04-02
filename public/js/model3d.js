import * as THREE from "https://cdn.skypack.dev/three@0.129.0/build/three.module.js";
import { GLTFLoader } from "https://cdn.skypack.dev/three@0.129.0/examples/jsm/loaders/GLTFLoader.js";

const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);

let object;
let animationActive = true;

const loader = new GLTFLoader();

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


        camera.position.set(-1, -10, -10);


        animate();
    }
);



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

    renderer.render(scene, camera);
}


window.addEventListener("resize", function () {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});