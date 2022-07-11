import React, { useEffect, useState } from 'react'
import { Text, StyleSheet, View, Image, TextInput, Dimensions, FlatList, Alert, ScrollView, SafeAreaView } from 'react-native'
import { Button, Card } from 'react-native-paper'
import Background from '../components/Background'
import Logo from '../components/Logo'
import Header from '../components/Header'
import Paragraph from '../components/Paragraph'
import axios from 'axios'
import { FlatGrid } from 'react-native-super-grid';
import { SwiperFlatList } from 'react-native-swiper-flatlist';


export default function BarangScreen({ navigation }) {
    const [data, setData] = useState([]);
    // const renderItem = ({item}) => (   
    // <View style={{marginTop: 15, flex: 1}}>
    //     <Card style={styles.card}>
    //         <Image source={{ uri: 'http://192.168.43.22:8000/storage/' + item.gambar,}} style={{width: '100%', height: 170}}/>
    //         <Text style={styles.judul_barang}>{item.nama_barang}</Text>
    //         <Text style={styles.judul_barang}>Rp{item.harga}</Text>  
    //     </Card>  
    // </View>
    // );
    
    useEffect(() => {
        async function getBarang(){
        let headers = {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            }
        }
        axios.get('http://192.168.43.22:8000/api/user/barang', headers).then(response => {
        console.log(response.data);
        setData(response.data);
            });
        }
        getBarang()
    },[]);
            return(
            <ScrollView showsHorizontalScrollIndicator={false}>
            <View>
                <View style={{ flexDirection: 'row' }}>
                    <View style={{ flex: 9 }}>
                        <TextInput
                            placeholder='Search'
                            underlineColorAndroid='transparent'
                            style={styles.form}
                            theme={{ roundness: 25 }} />
                    </View>
                    <View style={{ flex: 2 }}>
                        <Image source={{uri: 'https://toppng.com/uploads/preview/smile-icon-115494032680kbj2rhihk.png',}} style={styles.button} />
                    </View>
                </View>
                <View style={styles.container}>
                    <SwiperFlatList autoplay autoplayLoop>
                        <View style={[styles.child, { backgroundColor: 'tomato' }]}>
                            <Text style={styles.text} onPress={() => navigation.replace('RegisterScreen')}>1</Text>
                        </View>
                        <View style={[styles.child, { backgroundColor: 'thistle' }]}>
                            <Text style={styles.text}>2</Text>
                        </View>
                        <View style={[styles.child, { backgroundColor: 'skyblue' }]}>
                            <Text style={styles.text}>3</Text>
                        </View>
                        <View style={[styles.child, { backgroundColor: 'teal' }]}>
                            <Text style={styles.text}>4</Text>
                        </View>
                    </SwiperFlatList>
                </View>
                
                    {/* {data.map(function(postData){
                    return( 
                        <View style={{ flexDirection: 'row', marginTop: 15 }}>
                            <Image source={{ uri: 'http://192.168.43.22:8000/storage/' + postData.gambar, }} style={styles.image} />
                            <Text style={{ fontSize: 18 }}>{postData.nama_barang}</Text>
                        </View>
                        )
                    }
                    )} */}
                <SafeAreaView>
                    {/* <FlatList
                        keyExtractor={(item) => item.id_barang}
                        numColumns={2}
                        data={data} //pass in our data array
                        renderItem={renderItem} 
                        
                    /> */}
                </SafeAreaView>
                <FlatGrid
      itemDimension={130}
      data={data}
      style={{marginTop: 10,
        flex: 1,}}
      // staticDimension={300}
      // fixed
      spacing={10}
      renderItem={({ item }) => (
        <View style={{justifyContent: 'flex-end',
        borderRadius: 5,
        padding: 10,
        height: 150, color: 'red'}}>
          <Text style={styles.itemName}>{item.nama_barang}</Text>
          <Text style={{fontWeight: '600',
    fontSize: 12,
    color: '#fff',}}>{item.ram}</Text>
        </View>
      )}
    />
            </View>
            </ScrollView>
            )
        
}

const { width } = Dimensions.get('window');
const styles = StyleSheet.create({
    button:{
        width: 50,
        height: 50,
        borderRadius: 50,
        backgroundColor: 'grey',
        marginTop: 15,
        marginLeft: 5,
        marginRight: 10,
    },
    card:{
        marginHorizontal: 10,
        backgroundColor: 'transparent',
        borderTopLeftRadius: 8, 
        borderTopRightRadius: 8,
        borderWidth: 1,
    },
    image:{
        marginHorizontal: 10,
        marginTop: 10,
        width: 150,
        height: 150,
        borderRadius: 10,
        borderWidth: 1,
        borderColor: 'grey',
    },
    form:{
        marginTop: 15,
        borderColor: 'black',
        borderRadius: 25,
        height: 50,
        borderWidth: 1,
        marginLeft: 10,
        marginRight: 10,
        padding: 3,
        fontSize: 18,
        color: 'black'
    },
    carousel:{
        height: 250
    },
    container: {
        backgroundColor: 'white',
        marginTop: 15,
        height: 200,
    },
    child: { 
        width,
        justifyContent: 'center' 
    },
    text: { 
        fontSize: 50,
        textAlign: 'center' 
    },
    judul_barang: {
        marginVertical: 3,
        fontSize: 18,
        marginHorizontal: 10,
        color: "black"
    }
})

  